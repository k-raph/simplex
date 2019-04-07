<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 19:26
 */

namespace Simplex\Strategy;


use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Routing\Middleware\AbstractStrategy;
use Simplex\Routing\Middleware\StrategyMiddleware;
use Symfony\Component\HttpFoundation\Response;

class WebStrategy extends AbstractStrategy
{

    /**
     * WebMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->middlewares = $container->get(Configuration::class)
            ->get('routing.middlewares.web', []);
    }

    /**
     * Create a valid response based on strategy
     *
     * @param $response Response|mixed
     *
     * @return mixed
     */
    protected function createResponse($response): ?Response
    {
        if (
            is_string($response) ||
            (is_object($response) && method_exists($response, '__toString'))
        ) {
            return new Response((string)$response);
        }

        return null;
    }
}