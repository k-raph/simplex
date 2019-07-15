<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 04:49
 */

namespace App\AuthModule;

use App\AuthModule\Command\CreateUserCommand;
use App\AuthModule\Provider\DatabaseUserProvider;
use League\Container\Container;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Database\DatabaseInterface;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;

class AuthModuleProvider extends AbstractModule
{

    /**
     * AuthModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(Configuration::class);
        $table = $config->get('auth.providers.database.table', 'users');
        $field = $config->get('auth.login_field', 'email');

        /** @var Container $container */
        $container->add(DatabaseUserProvider::class)
            ->addArgument(DatabaseInterface::class)
            ->addArgument($table)
            ->addArgument($field);
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

    /**
     * @param TwigRenderer $renderer
     * @throws \Twig_Error_Loader
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'auth');
    }

    /**
     * @param RouteCollection $collection
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->match('GET', '/login', 'App\AuthModule\Actions\AccessAction:loginForm', 'auth_login');
        $collection->match('POST', '/login', 'App\AuthModule\Actions\AccessAction:attemptLogin');
        $collection->match('POST', '/logout', 'App\AuthModule\Actions\AccessAction:logout', 'auth_logout');
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return [
            CreateUserCommand::class
        ];
    }
}