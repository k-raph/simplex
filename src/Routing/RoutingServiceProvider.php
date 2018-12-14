<?php

namespace Simplex\Routing;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class RoutingServiceProvider extends AbstractServiceProvider
{

    /**
     * {@inheritDoc}
     */
    protected $provides = [
        RouterInterface::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $loader = new YamlFileLoader(new FileLocator());
        $router = new SymfonyRouter($loader);

        $this->container->add(RouterInterface::class, $router);
    }

}