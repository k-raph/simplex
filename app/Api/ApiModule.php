<?php

namespace App\Api;

use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiModule
{

    /**
     * Constructor
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $router->import(__DIR__.'/routes.yml', 'api');
    }

}