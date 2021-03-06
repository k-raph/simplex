<?php

namespace Simplex\Middleware;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Container\ContainerInterface;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Simplex\Routing\Middleware\DispatchMiddleware;
use Simplex\Routing\Middleware\StrategyInterface;
use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoutingMiddleware implements MiddlewareInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param ContainerInterface $container
     */
    public function __construct(RouterInterface $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->setContainer($container);
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->route($request);

        /** @var StrategyInterface $strategy */
        $strategy = $request->attributes
            ->get('_route')
            ->getStrategy();

        $strategy->add($this->container->get(DispatchMiddleware::class));

        return $strategy->process($request, $handler);
    }

    /**
     * Route the request
     *
     * @param Request $request
     */
    private function route(Request $request)
    {
        $route = $this->router->dispatch($request);

        $strategy = $route->getStrategy();
        foreach ($route->getMiddlewares() as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->container->get($middleware);
            }
            $strategy->add($middleware);
        }

        $request->attributes->set('_route', $route);
    }
}
