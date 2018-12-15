<?php

namespace Simplex\Routing;

use Symfony\Component\HttpFoundation\Request;
use Simplex\Http\MiddlewareInterface;

interface RouterInterface
{

    /**
     * Load routes from given file and return corresponding route
     *
     * @param string $file
     * @return void
     */
    public function import($from, $prefix = '/');

    /**
     * Match only given HTTP methods
     *
     * @param string $methods
     * @param string $path
     * @param string $controller
     * @param string $name
     * @return Route
     */
    public function match($methods, $path, $controller, $name = null);

    /**
     * Mount a set of routes under a common prefix
     *
     * @param string $prefix
     * @param \Closure $factory
     * @return void
     */
    // public function group($prefix, \Closure $factory);

    /**
     * Dispatches a request
     *
     * @param Request $request
     * @return Route
     */
    public function dispatch(Request $request): Route;

    /**
     * Generate url for given route name
     *
     * @param string $name
     * @param array $parameters
     * @return void
     */
    public function generate(string $name, array $parameters = []): string;

    /**
     * Add route specific middlewares to router
     *
     * @param MiddlewareInterface $middleware
     * @return void
     */
    public function middleware(MiddlewareInterface $middleware);
}