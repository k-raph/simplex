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

class Kernel
{

    /**
     * Container
     *
     * @var Container
     */
    protected $container;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->container->delegate(new ReflectionContainer);
        $this->bootstrap();
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
            $route = $this->container->get(RouterInterface::class)->dispatch($request);
            $result = $this->container->call($route->getCallback(), array_merge(compact('request'), $route->getParams()));
            
            if (!($result instanceof Response)) {
                if (is_string($result))
                    return new Response($result);
                elseif (is_array($result)) {
                    return  new JsonResponse($result);
                } else 
                    throw new \LogicException(sprintf('Controller must return a string,an array or a Response object. "%s" given', gettype($result)));
            }

            return $result;
        } catch (ResourceNotFoundException $e) {
            return new Response($e->getMessage(), 404);
        } catch (MethodNotAllowedException $e) {
            return new Response($e->getMessage(), 405, ['Allow' => 'GET']);
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