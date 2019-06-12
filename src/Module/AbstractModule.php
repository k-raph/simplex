<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:32
 */

namespace Simplex\Module;

use Simplex\Configuration\Configuration;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\Routing\RouteCollectionBuilder;

abstract class AbstractModule implements ModuleInterface
{

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
     * @param RouteCollectionBuilder $builder
     */
    public function getAdminRoutes(RouteCollectionBuilder $builder): void
    {
        // TODO: Implement getAdminRoutes() method.
    }

    /**
     * @param RouteCollectionBuilder $builder
     */
    public function getSiteRoutes(RouteCollectionBuilder $builder): void
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

}