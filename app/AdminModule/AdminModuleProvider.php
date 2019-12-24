<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 15:39
 */

namespace App\AdminModule;

use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouteCollection;
use Simplex\Module\AbstractModule;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

class AdminModuleProvider extends AbstractModule
{

    /**
     * @param RouteCollection $collection
     * @throws FileLoaderLoadException
     */
    public function getAdminRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml');//, [
        /*    'prefix' => 'admin',
        ]);*/
    }

    /**
     * @param TwigRenderer $renderer
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
