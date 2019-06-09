<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 04:49
 */

namespace App\Auth;

use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;

class AuthModule extends AbstractModule
{

    /**
     * AuthModule constructor.
     * @param RouterInterface $router
     * @param TwigRenderer $renderer
     */
    public function __construct(RouterInterface $router, TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'auth');

        $router->match('GET', '/login', 'App\Auth\Actions\AccessAction:loginForm', 'auth_login');
        $router->match('POST', '/login', 'App\Auth\Actions\AccessAction:attemptLogin');
        $router->match('POST', '/logout', 'App\Auth\Actions\AccessAction:logout', 'auth_logout');
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'auth';
    }
}