<?php

namespace Simplex\Middleware;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Container\ContainerInterface;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\Pipeline;
use Simplex\Http\RequestHandlerInterface;
use Simplex\Routing\Middleware\DispatchMiddleware;
use Simplex\Routing\Middleware\RouteMiddleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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