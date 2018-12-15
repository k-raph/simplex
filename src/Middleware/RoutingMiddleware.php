<?php

namespace Simplex\Middleware;

use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Simplex\Routing\RouterInterface;
use Simplex\Http\Pipeline;

class RoutingMiddleware implements MiddlewareInterface
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