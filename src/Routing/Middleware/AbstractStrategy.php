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

abstract class AbstractStrategy implements StrategyInterface
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

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->pipeline->seed($this->middlewares, [$this->container, 'get']);

        return $this->pipeline->process($request, $handler);
    }

    /**
     * @param $response Response|mixed
     * @return Response
     */
    public function handle($response): Response
    {
        $response = $response instanceof Response
            ? $response
            : $this->createResponse($response);

        if (!$response)
            throw new \Exception(sprintf('Unable to build a proper response. Got response of type: "%s"', gettype($response)));

        return $response;
    }

    /**
     * @param MiddlewareInterface|string $middleware
     */
    public function add(MiddlewareInterface $middleware)
    {
        $this->pipeline->pipe($middleware);
    }

    /**
     * Create a valid response based on strategy
     *
     * @param $result Response|mixed
     *
     * @return Response|null
     */
    abstract protected function createResponse($result): ?Response;
}