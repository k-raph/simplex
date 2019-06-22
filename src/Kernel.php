<?php

namespace Simplex;

use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\EventManager\EventManager;
use Simplex\EventManager\EventManagerInterface;
use Simplex\Events\KernelBootEvent;
use Simplex\Events\KernelRequestEvent;
use Simplex\Events\KernelResponseEvent;
use Simplex\Http\Pipeline;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tracy\Debugger;

class Kernel
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Pipeline
     */
    protected $pipeline;

    /**
     * Kernel is booted?
     *
     * @var boolean
     */
    protected $booted = false;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $container = new Container();
        $container->defaultToShared();
        $container->delegate((new ReflectionContainer)->cacheResolutions(true));
        $container->add(ContainerInterface::class, $container);

        $this->pipeline = new Pipeline();
        $this->eventManager = new EventManager();

        $container->add(Pipeline::class, $this->pipeline);
        $container->add(EventManagerInterface::class, $this->eventManager);

        $this->container = $container;
        $this->bootstrap();
    }

    /**
     * Load config files
     *
     * @return void
     */
    public function configure(): void
    {
        try {
            $root = dirname(__DIR__);
            $path = "$root/config";
            $locator = new FileLocator($path);

            $config = new Configuration([
                'root' => $root,
                'resources' => "$root/resources"
            ]);

            foreach (glob("$path/*") as $file) {
                $info = pathinfo($file);
                $namespace = $info['filename'];

                $config->load(
                    $locator->locate($info['basename']),
                    ('config' === $namespace || 'bootstrap' === $namespace)
                        ? null
                        : $namespace
                );
            }
            $this->container->add(Configuration::class, $config);
        } catch (FileLocatorFileNotFoundException $e) {
            throw new \RuntimeException('Config files not found!');
        }
      
    }

    /**
     * Bootstrap container
     *
     * @return void
     */
    private function bootstrap(): void
    {
        $this->configure();

        $providers = $this->container
            ->get(Configuration::class)
            ->get('providers', []);

        foreach ($providers as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }
    
    /**
     * Boot the kernel
     *
     * @return void
     */
    private function boot(): void
    {
        if ($this->booted)
            return;

        $config = $this->container->get(Configuration::class);
        /** @var Configuration $config */
        $config = $this->eventManager->dispatch(new KernelBootEvent($config))->getConfiguration();

        if ('debug' === $config->get('env')) {
            Debugger::enable();
        }

        // Register middlewares
        $pipes = $config->get('routing.middlewares.global', []);
        foreach ($pipes as $key => $middleware) {
            if (is_array($middleware)) {
                continue;
            }
            $this->pipeline->pipe($this->container->get($middleware));
        }

        $this->booted = true;
    }

    /**
     * Handle the request
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        $this->boot();
        $request::enableHttpMethodParameterOverride();

        $event = $this->eventManager->dispatch(new KernelRequestEvent($request));

        $response = $event->isPropagationStopped()
            ? $event->getResponse()
            : $this->pipeline->handle($request);

        /** @var KernelResponseEvent $event */
        $event = $this->eventManager->dispatch(new KernelResponseEvent($response));
        return $event->getResponse();
    }

    /**
     * Terminate request handling
     *
     * @param Response $response
     * @param Request $request
     * @return void
     */
    public function terminate(Response $response, Request $request)
    {
        $response->prepare($request)->send();
    }
}