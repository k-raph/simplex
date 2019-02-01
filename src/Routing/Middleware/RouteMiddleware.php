<?php

namespace Simplex\Routing\Middleware;

use Simplex\Http\MiddlewareInterface;
use Simplex\Http\Pipeline;
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
     * Constructor
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        /* @var \Simplex\Routing\Route $route */
        $route = $this->router->dispatch($request);
        $request->attributes->set('_route', $route);

        $pipeline = new Pipeline();
        foreach ($route->getMiddlewares() as $middleware) {
            $pipeline->pipe($middleware);
        }

        return $pipeline->process($request, $handler);
    }

}