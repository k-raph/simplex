<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 15:39
 */

namespace App\AdminModule;

use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;

class AdminModuleProvider extends AbstractModule
{

    /**
     * @param RouteCollection $collection
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function getAdminRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml', [
            //'prefix' => 'admin',
        ]);
    }

    /**
     * @param TwigRenderer $renderer
     * @throws \Twig_Error_Loader
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'admin');
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'admin';
    }
}
