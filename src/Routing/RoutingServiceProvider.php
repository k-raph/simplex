<?php

namespace Simplex\Routing;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Simplex\Http\Pipeline;

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
        $groups = $this->container->get('config')['middlewares']['groups'];
        foreach ($groups as $group => $pipes) {
            $router->middleware((new Pipeline())->seed($pipes, [$this->container, 'get']), $group);
        }
        $router->setStrategy('web');

        $this->container->add(RouterInterface::class, $router);
    }

}