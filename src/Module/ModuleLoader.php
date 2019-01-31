<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:07
 */

namespace Simplex\Module;


use Psr\Container\ContainerInterface;

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
    }
}