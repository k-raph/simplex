<?php

namespace Simplex\Routing;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

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

        // Register strategies on router
        foreach ($this->container->get(Configuration::class)->get('routing.strategies') as $name => $strategy) {
            $router->addStrategy($name, $this->container->get($strategy));
        }
        $router->setStrategy('web');

        $this->container->add(RouterInterface::class, $router);
    }

}