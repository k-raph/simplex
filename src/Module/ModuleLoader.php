<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:07
 */

namespace Simplex\Module;


use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\DataMapper\DataMapperServiceProvider;
use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMapperInterface;

class ModuleLoader
{

    /**
     * @var ModuleInterface[]
     */
    private $modules = [];
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ModuleLoader constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string[] $modules
     */
    public function load(array $modules)
    {
        foreach ($modules as $module) {
            if (is_subclass_of($module, ModuleInterface::class)) {
                $module = $this->container->get($module);
                $this->modules[$module->getName()] = $module;
            }
        }

        $this->bootstrap();
    }

    /**
     * Bootstrap the modules
     */
    public function bootstrap()
    {
        /** @var Configuration $config */
        $config = $this->container->get(Configuration::class);
        $map = class_exists(EntityManager::class) && in_array(DataMapperServiceProvider::class, $config->get('providers', []));

        $mappings = [];
        foreach ($this->modules as $module) {
            $module->configure($config);
            $mappings = array_merge($mappings, $module->getMappings());
        }

        if ($map) {
            /** @var EntityManager $manager */
            $manager = $this->container->get(EntityManager::class);
            $registry = $manager->getMapperRegistry();
            $registry->setResolver([$this->container, 'get']);

            foreach ($mappings as $class => $mapper) {
                if (is_subclass_of($mapper, EntityMapperInterface::class)) {
                    $registry->register($class, $mapper);
                }
            }
        }
    }
}