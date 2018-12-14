<?php

namespace App\Api;

use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Simplex\Database\Connection;

class ApiModule
{

    /**
     * Constructor
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, Connection $conn)
    {
        var_dump($conn->getPdo()->query("SELECT * FROM sqlite_master WHERE type = 'table' ORDER BY name")
            ->fetchAll(\PDO::FETCH_ASSOC));
        $router->import(__DIR__.'/routes.yml');
    }

}