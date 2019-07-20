<?php

namespace Simplex\Routing\Middleware;

use Psr\Container\ContainerInterface;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteMiddleware implements MiddlewareInterface
{

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param ContainerInterface $container
     */
    public function __construct(RouterInterface $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        /* @var \Simplex\Routing\Route $route */
        $route = $this->router->dispatch($request);
        $request->attributes->set('_route', $route);

        $strategy = $route->getStrategy();
        foreach ($route->getMiddlewares() as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->container->get($middleware);
            }
            $strategy->add($middleware);
        }

        return $strategy->process($request, $handler);
    }
}
