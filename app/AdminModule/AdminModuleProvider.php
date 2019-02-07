<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 15:39
 */

namespace App\AdminModule;


use Simplex\Middleware\AuthenticationMiddleware;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;

class AdminModuleProvider extends AbstractModule
{

    /**
     * AdminModuleProvider constructor.
     * @param RouterInterface $router
     * @param TwigRenderer $renderer
     */
    public function __construct(RouterInterface $router, TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'admin');
        $router->import(__DIR__ . '/resources/routes.yml', [
            'prefix' => 'admin',
            '_middlewares' => [AuthenticationMiddleware::class]
        ]);
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