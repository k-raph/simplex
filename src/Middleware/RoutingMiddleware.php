<?php

namespace Simplex\Middleware;

use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Simplex\Routing\RouterInterface;

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
        $route = $this->router->dispatch($request);
        $request->attributes->set('_route', $route);

        return $handler->handle($request);
    }

}