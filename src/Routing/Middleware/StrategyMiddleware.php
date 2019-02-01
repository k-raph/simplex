<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 19:13
 */

namespace Simplex\Routing\Middleware;


use Psr\Container\ContainerInterface;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\Pipeline;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class StrategyMiddleware implements MiddlewareInterface
{

    /**
     * @var array
     */
    protected $middlewares = [];
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Pipeline
     */
    private $pipeline;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pipeline = new Pipeline();
    }

    /**
     * Process an incoming HTTP Request and returns a Response
     *
     * @param Request $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->pipeline->seed($this->middlewares, [$this->container, 'get']);

        return $this->pipeline->process($request, $handler);
    }
}