<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05/05/2019
 * Time: 12:03
 */

namespace Simplex\Module;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Simplex\Configuration\Configuration;

class ModuleServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    protected $provides = [
        ModuleLoader::class
    ];

    /**
     * @var ModuleLoader
     */
    private $loader;

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->add(ModuleLoader::class, $this->loader);
    }

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     *
     * @return void
     */
    public function boot()
    {
        /** @var Configuration $config */
        $config = $this->container->get(Configuration::class);

        // Load modules
        $modules = $config->get('modules', []);
        $loader = new ModuleLoader($this->container);
        $loader->load($modules);
        $this->loader = $loader;
    }
}
