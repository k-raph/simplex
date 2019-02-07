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
        $config = $this->container->get(Configuration::class);

        $loader = new YamlFileLoader(new FileLocator());
        $router = new SymfonyRouter($loader, $config->get('app_host', 'localhost'));

        // Register strategies on router
        foreach ($config->get('routing.strategies') as $name => $strategy) {
            $router->addStrategy($name, $this->container->get($strategy));
        }
        $router->setStrategy('web');

        $router->get('/', 'Simplex\Routing\RoutingServiceProvider:default', 'simplex_home');

        $this->container->add(RouterInterface::class, $router);
    }

    public function default()
    {
        return <<<HTML
<div style='text-align: center; font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif; font-size: x-large'>
    <h1>Welcome to Simplex default home</h1>
</div>
HTML;

    }
}