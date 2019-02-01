<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 19:26
 */

namespace Simplex\Middleware;


use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Routing\Middleware\StrategyMiddleware;

class WebMiddleware extends StrategyMiddleware
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

}