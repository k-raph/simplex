<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:32
 */

namespace Simplex\Module;

use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouteCollection;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;

abstract class AbstractModule implements ModuleInterface
{

    public function register(ContainerInterface $container)
    {
        // TODO: Implement register() method.
    }

    /**
     * @param Configuration $configuration
     * @return mixed|void
     */
    public function configure(Configuration $configuration)
    {
        // TODO: Implement loadConfig() method.
    }

    /**
     * @return array
     */
    public function getMappings(): array
    {
        return [];
    }

    /**
     * @param RouteCollection $collection
     */
    public function getAdminRoutes(RouteCollection $collection): void
    {
        // TODO: Implement getAdminRoutes() method.
    }

    /**
     * @param RouteCollection $collection
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        // TODO: Implement getSiteRoutes() method.
    }

    /**
     * @param TwigRenderer $renderer
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        // TODO: Implement registerTemplates() method.
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return [];
    }

    /**
     * @return string|null
     */
    public function getMigrationsConfig(): ?string
    {
        return null;
    }
}
