<?php

namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\FileLocator;
use Simplex\Routing\RouterInterface;
use Simplex\Routing\SymfonyRouter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use League\Event\Emitter;
use Simplex\Event\RequestEvent;
use Simplex\Listener\TrailingSlashListener;
use Simplex\Event\ViewEvent;
use Simplex\Listener\ViewEventListener;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Routing\Loader\DirectoryLoader;
use Simplex\Listener\ResolveControllerListener;
use Simplex\Event\ControllerEvent;
use League\Container\Exception\NotFoundException;
use Simplex\Database\Connection;
use Simplex\Renderer\TwigServiceProvider;
use Simplex\Renderer\TwigRenderer;

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
     * Constructor
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->container->delegate(new ReflectionContainer);
        $this->bootstrap();
        $this->configure();
    }
  
    public function configure()
    {
        try {
            $path = dirname(__DIR__).'/resources';
            $locator = new FileLocator($path);
            $config = Yaml::parseFile($locator->locate('config.yml'));
            
            $this->container->share(Connection::class, function () use ($config) {
                if (isset($config['database']))
                return new Connection($config['database']['type'], $config['database']);
            });

            $this->loadModules($config['modules']);
        } catch (FileLocatorFileNotFoundException $e) {
            
        }
      
    }

    /**
     * Load modules from config file
     *
     * @return void
     */
    protected function loadModules(array $modules)
    {
        foreach($modules as $module) {
            $this->modules[] = $this->container->get($module);
        }
    }

    /**
     * Bootstrap container
     *
     * @return void
     */
    private function bootstrap()
    {
        $this->container->share(LoaderInterface::class, function() {
            return new YamlFileLoader(new FileLocator());
        });
        $this->container->share(RouterInterface::class, SymfonyRouter::class)
            ->withArgument(LoaderInterface::class);
        $this->container->share(Emitter::class);
        $this->container->addServiceProvider(TwigServiceProvider::class);
        $this->emitter = $this->container->get(Emitter::class);
        $this->registerEvents();
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
    }

    /**
     * Handle the request
     *
     * @param Request $request
     * @return Resonse
     */
    public function handle(Request $request)
    {
        $this->container->share(Request::class, $request);

        try {
            $event = new RequestEvent($request);
            $this->emitter->emit($event);
            if ($event->hasResponse())
                return $event->getResponse();

            $route = $this->container->get(RouterInterface::class)->dispatch($request);
            
            $event = new ControllerEvent($route->getCallback(), $request);
            $this->emitter->emit($event);

            $response = $this->container->call($event->getController(), $event->getParams());
            
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
        } elseif ($exception instanceof NotFoundException) {
            return new Response($exception->getMessage(), 500);
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