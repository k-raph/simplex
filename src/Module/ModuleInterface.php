<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:07
 */

namespace Simplex\Module;


use Simplex\Configuration\Configuration;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\Routing\RouteCollectionBuilder;

interface ModuleInterface
{
    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Register configuration
     *
     * @param Configuration $configuration
     * @return mixed
     */
    public function configure(Configuration $configuration);

    /**
     * Get entity maps provided by the module
     *
     * @return array
     */
    public function getMappings(): array;

    /**
     * Register backend routes
     *
     * @param RouteCollectionBuilder $builder
     * @return void
     */
    public function getAdminRoutes(RouteCollectionBuilder $builder): void;

    /**
     * Register frontend routes
     *
     * @param RouteCollectionBuilder $builder
     */
    public function getSiteRoutes(RouteCollectionBuilder $builder): void;

    /**
     * Register view templates
     *
     * @param TwigRenderer $renderer
     * @return void
     */
    public function registerTemplates(TwigRenderer $renderer);
}