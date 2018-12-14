<?php

namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Config\FileLocator;
use Simplex\Routing\RouterInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use League\Event\Emitter;
use Simplex\Event\RequestEvent;
use Simplex\Listener\TrailingSlashListener;
use Simplex\Event\ViewEvent;
use Simplex\Listener\ViewEventListener;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Simplex\Listener\ResolveControllerListener;
use Simplex\Event\ControllerEvent;
use League\Container\Exception\NotFoundException;
use Simplex\Renderer\TwigServiceProvider;
use Simplex\Routing\RoutingServiceProvider;
use Simplex\Listener\ResolveArgumentListener;

class Kernel
{

    /**
     * Container
     *
     * @var Container
     */
    protected $container;

    /**
     * Event emitter
     *
     * @var Emitter
     */
    protected $emitter;

    /**
     * Registered modules
     *
     * @var array
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
        
        $this->container = $container;
        $this->emitter = $this->container->get(Emitter::class);
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
            $path = dirname(__DIR__).'/resources';
            $locator = new FileLocator($path);
            $config = Yaml::parseFile($locator->locate('config.yml'));
            $this->container->add('config', $config);
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

        $providers = $this->container->get('config')['providers'] ?? [];
        foreach ($providers as $provider) {
            $this->container->addServiceProvider($provider);
        }
        
        $this->registerEvents();
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
        
        $modules = $this->container->get('config')['modules'] ?? [];
        
        foreach($modules as $module) {
            $this->modules[] = $this->container->get($module);
        }

        $this->booted = true;
    }

    /**
     * Register listeners against their events
     *
     * @return void
     */
    protected function registerEvents()
    {
        $this->emitter->addListener('kernel.request', new TrailingSlashListener);
        $this->emitter->addListener('kernel.view', new ViewEventListener);
        $this->emitter->addListener('kernel.controller', new ResolveControllerListener($this->container));
        $this->emitter->addListener('kernel.controller', new ResolveArgumentListener($this->container));
    }

    /**
     * Handle the request
     *
     * @param Request $request
     * @return Resonse
     */
    public function handle(Request $request)
    {
        $this->container->add(Request::class, $request);

        try {
            $this->boot();

            $event = new RequestEvent($request);
            $this->emitter->emit($event);
            if ($event->hasResponse())
                return $event->getResponse();

            $route = $this->container->get(RouterInterface::class)->dispatch($request);
            
            $event = new ControllerEvent($route->getCallback(), $request);
            $this->emitter->emit($event);

            $response = call_user_func_array($event->getController(), $event->getParams());

            if (!($response instanceof Response)) {
                $event = new ViewEvent($response);
                $this->emitter->emit($event);
                $response = $event->getResponse();
            }

            return $response;
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
            return new Response($exception->getMessage(), 405, ['Allow' => implode(', ', $exception->getAllowedMethods())]);
        // } elseif ($exception instanceof NotFoundException) {
        //     return new Response($exception->getMessage(), 500);
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