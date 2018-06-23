<?php

namespace Simplex\Routing;

use Symfony\Component\Routing\Route as SymfonyRoute;


class Route
{
    /**
     * Encapsulated route
     *
     * @var SymfonyRoute
     */
    private $route;
       
    /**
     * Route parameters
     *
     * @var string[]
     **/
    private $parameters;
       

    public function __construct($path, $callback, array $parameters = [])
    {
        $this->route = new SymfonyRoute($path);
        $this->route->setDefault('_controller', $callback);
        $this->parameters = $parameters;
    }

    /**
     * Build from Symfony Route
     *
     * @param SymfonyRoute $baseRoute
     * @param array $with
     * @return static
     */
    public static function from(SymfonyRoute $baseRoute, array $with = [])
    {
        $route = new static('path', 'callback', $with);
        $route->setRoute($baseRoute);
        return $route;
    }

    /**
     * Set encapsulated route
     *
     * @param Route $route
     * @return void
     */
    protected function setRoute(SymfonyRoute $route)
    {
        $this->route = $route;
    }
     
    /**
     * Gets the callback
     *
     * @return string|callable
     **/
    public function getCallback()
    {
        return $this->route->getDefault('_controller');
    }
       
    /**
     * Retrieve an URL parameters
     *
     * @return string[]
     **/
    public function getParams()
    {
        return $this->parameters;
    }

    /**
     * Set requirement on route dynamic parts
     *
     * @param string $param
     * @param string $regex
     * @return void
     */
    public function assert($param, $regex)
    {
        $this->route->setRequirement($param, $regex);
        return $this;
    }

}
