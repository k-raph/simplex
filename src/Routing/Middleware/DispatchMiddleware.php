<?php

namespace Simplex\Routing\Middleware;

use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\Argument\ArgumentResolverTrait;
use League\Container\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

class DispatchMiddleware implements MiddlewareInterface, ContainerAwareInterface
{

    use ArgumentResolverTrait, ContainerAwareTrait;

    /**
     * Constructor
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
        /** @var \Simplex\Routing\Route */
        $route = $request->attributes->get('_route');
        $controller = $this->resolveController($route->getHandler());
        
        $reflected = \is_array($controller)
            ? new \ReflectionMethod($controller[0], $controller[1])
            : new \ReflectionFunction($controller);

        $args = $this->reflectArguments($reflected, array_merge($route->getParameters(), compact('request')));

        $result = call_user_func_array($controller, $args);

        return $this->validateResponse($result);
    }

    /**
     * Resolve controller into callable
     *
     * @param string|array|callable $controller
     * @return callable
     */
    private function resolveController($controller): callable
    {
        if (is_string($controller)) {
            if (strpos($controller, '::') !== false) {
                $controller = explode('::', $controller);
            } elseif (strpos($controller, ':') !== false) {
                $controller = explode(':', $controller);
            } elseif (method_exists($controller, '__invoke')) {
                $controller = [$this->container->get($controller), '__invoke'];
            }
        }
        
        if (is_array($controller)) {
            $controller[0] = is_object($controller[0])
                ? $controller[0]
                : $this->container->get($controller[0]);
        }

        return $controller;
    }

    /**
     * Validate controller response
     *
     * @param string|array|Response $response
     * @return Response
     */
    private function validateResponse($response): Response
    {
        if (!$response instanceof Response) {
            if (
                is_string($response) || 
                (is_object($response) && method_exists($response, '__toString'))
            ) {
                $response = new Response((string)$response);
            } elseif (
                is_array($response) ||
                (is_object($response) && $response instanceof \ArrayAccess)
            ) {
                $response = new JsonResponse((array)$response);
            } else 
                throw new \LogicException(sprintf('Controller must return a string, an array or a Response object. "%s" given', gettype($response)));
        }

        return $response;
    }
}