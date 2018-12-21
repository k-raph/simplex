<?php

namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Container\ContainerInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use League\Container\Exception\NotFoundException;
use Simplex\Http\Pipeline;
use Simplex\Database\Exception\ResourceNotFoundException as DatabaseResourceNotFoundException;

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
        
        $config =  $this->container->get('config');

        // Register middlewares
        $pipes = $config['middlewares'] ?? [];
        foreach ($pipes as $key => $middleware) {
            if (is_array($middleware)) {
                continue;
            }
            $this->pipeline->pipe($this->container->get($middleware));
        }

        // Load modules
        $modules = $config['modules'] ?? [];
        $this->modules = array_map(function ($module) {
            return $this->container->get($module);
        }, $modules);

        $this->booted = true;
    }

    /**
     * Handle the request
     *
     * @param Request $request
     * @return Resonse
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
        if ($exception instanceof ResourceNotFoundException || $exception instanceof DatabaseResourceNotFoundException) {
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