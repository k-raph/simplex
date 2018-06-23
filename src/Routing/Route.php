<?php

namespace Simplex\Routing;

class Route
{
       
    /**
     * Route name
     *
     * @var string
     **/
    private $name;
       
    /**
     * Callback to invoke when route was matched
     *
     * @var callable|string
     **/
    private $callback;
       
    /**
     * Route parameters
     *
     * @var string[]
     **/
    private $parameters;
       
    /**
     * Constructor
     *
     * @param string $name
     * @param string $callback
     * @param string[] $parameters
     */
    public function __construct($name, $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }
       
    /**
     * Get route name
     *
     * @return string
     **/
    public function getName()
    {
        return $this->name;
    }
       
    /**
     * Gets the callback
     *
     * @return string|callable
     **/
    public function getCallback()
    {
        return $this->callback;
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
}
