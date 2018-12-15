<?php

namespace Simplex\Routing;

class Route
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var callable
     */
    private $handler;

    /**
     * @var array
     **/
    private $parameters;
       

    /**
     * Constructor
     *
     * @param string $name
     * @param string|callable $handler
     * @param array $parameters
     */
    public function __construct(string $name, $handler, array $parameters = [])
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->parameters = $parameters;
    }
     
    /**
     * Gets the callback
     *
     * @return string|callable
     **/
    public function getHandler()
    {
        return $this->handler;
    }
       
    /**
     * Retrieve an URL parameters
     *
     * @return array
     **/
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
