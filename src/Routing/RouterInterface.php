<?php

namespace Simplex\Routing;

use Symfony\Component\HttpFoundation\Request;

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
     * @return void
     */
    public function match($methods, $path, $controller, $name = null);

    /**
     * Dispatches a request
     *
     * @param Request $request
     * @return Route
     */
    public function dispatch(Request $request);

    /**
     * Generate url for given route name
     *
     * @param string $name
     * @return void
     */
    // public function route($name);
}