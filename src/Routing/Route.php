<?php

namespace Simplex\Routing;

use Simplex\Http\MiddlewareInterface;
use Simplex\Routing\Middleware\StrategyInterface;

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
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @var StrategyInterface
     */
    private $strategy;

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

    /**
     * Sets route parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Get route middleware
     *
     * @return MiddlewareInterface[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Get route middleware
     *
     * @param MiddlewareInterface[]
     */
    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @param StrategyInterface $strategy
     */
    public function setStrategy(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return StrategyInterface
     */
    public function getStrategy(): StrategyInterface
    {
        return $this->strategy;
    }
}
