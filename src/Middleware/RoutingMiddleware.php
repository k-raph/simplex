<?php

namespace Simplex\Middleware;

use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use League\Container\ContainerAwareTrait;
use League\Container\ContainerAwareInterface;
use Simplex\Http\Pipeline;
use Simplex\Routing\RouterInterface;
use Simplex\Routing\Middleware\RouteMiddleware;
use Simplex\Routing\Middleware\DispatchMiddleware;
use Psr\Container\ContainerInterface;

class RoutingMiddleware implements MiddlewareInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * COnstructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        /** @var RouterInterface $router */
        $router = $this->container->get(RouterInterface::class);
        $pipeline = new Pipeline();
        
        $pipes = [
            RouteMiddleware::class, 
            DispatchMiddleware::class
        ];
        
        $pipeline->seed($pipes, function ($pipe) {
            return $pipe instanceof MiddlewareInterface 
                ? $pipe
                : $this->container->get($pipe);
        });

        return $pipeline->process($request, $handler);
    }

}