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
use Simplex\DataMapper\Mapping\MappingRegistry;
use Simplex\Middleware\AuthenticationMiddleware;
use Simplex\Renderer\TwigRenderer;
use Simplex\Renderer\TwigServiceProvider;
use Simplex\Routing\RouterInterface;
use Simplex\Routing\SymfonyRouter;

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

        /** @var TwigRenderer $twig */
        $twig = in_array(TwigServiceProvider::class, $config->get('providers', []))
            ? $this->container->get(TwigRenderer::class)
            : null;

        /** @var SymfonyRouter $router */
        $router = $this->container->get(RouterInterface::class);
        $admin = $router->createCollection();
        $admin->setDefault('_middlewares', [AuthenticationMiddleware::class]);

        $mappings = [];
        foreach ($this->modules as $module) {
            // Register configuration data
            $module->configure($config);

            // Register routes for front end back end
            $builder = $router->createCollection();
            $module->getAdminRoutes($admin);
            $module->getSiteRoutes($builder);
            $router->mount('/', $builder);

            //Register view bindings
            if ($twig) {
                $module->registerTemplates($twig);
            }

            // Register mappings
            array_push($mappings, $module->getMappings());
        }
        $router->mount('/admin', $admin);

        $map = class_exists(EntityManager::class) &&
            in_array(DataMapperServiceProvider::class, $config->get('providers', []));
        $this->loadMappings($map, $mappings);
    }

    /**
     * @param bool $registered
     * @param array $mappings
     */
    public function loadMappings(bool $registered, array $mappings)
    {
        if ($registered) {
            $registry = new MappingRegistry();

            foreach ($mappings as $mapping) {
                if (isset($mapping['mappings'])) {
                    $registry->register($mapping['mappings'], $mapping['connection'] ?? null);
                }
            }

            $this->container->add(MappingRegistry::class, $registry);
        }
    }

    /**
     * @return ModuleInterface[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function get(string $name): ?ModuleInterface
    {
        return $this->modules[$name] ?? null;
    }
}
