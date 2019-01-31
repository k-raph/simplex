<?php

namespace Simplex;

use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Http\Pipeline;
use Simplex\Module\ModuleLoader;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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
     * Registered modules
     *
     * @var ModuleLoader
     */
    protected $modules;

    /**
     * Kernel is booted?
     *
     * @var boolean
     */
    protected $booted = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $container = new Container();
        $container->defaultToShared();
        $container->delegate((new ReflectionContainer)->cacheResolutions(true));
        $container->add(ContainerInterface::class, $container);
        
        $this->container = $container;
        $this->pipeline = new Pipeline();
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
            $locator = new FileLocator("$root/config");
            $config = new Configuration([
                'root' => $root,
                'resources' => "$root/resources"
            ]);

            $config->load($locator->locate('config.yml'));
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

        if ('debug' === $config->get('env')) {
            Debugger::enable();
        }

        // Register middlewares
        $pipes = $config->get('middlewares', []);
        foreach ($pipes as $key => $middleware) {
            if (is_array($middleware)) {
                continue;
            }
            $this->pipeline->pipe($this->container->get($middleware));
        }

        // Load modules
        $modules = $config->get('modules', []);
        $loader = new ModuleLoader($this->container);
        $loader->load($modules);
        $this->modules = $loader;

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
        try {
            $this->boot();
            return $this->pipeline->handle($request);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle catched exception to return appropriate response
     *
     * @param \Exception $exception
     * @return null|Response
     */
    private function handleException(\Exception $exception)
    {
        if ($exception instanceof ResourceNotFoundException) {
            return new Response($exception->getMessage(), 404);
        } elseif ($exception instanceof MethodNotAllowedException) {
            return new Response(
                $exception->getMessage(), 
                405, 
                ['Allow' => implode(', ', $exception->getAllowedMethods())]
            );
        } else {
            throw $exception;
        }
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