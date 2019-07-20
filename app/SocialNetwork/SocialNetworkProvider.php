<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/05/2019
 * Time: 07:46
 */

namespace App\SocialNetwork;

use Simplex\Configuration\Configuration;
use Simplex\Middleware\AuthenticationMiddleware;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;

class SocialNetworkProvider extends AbstractModule
{

    public function __construct(TwigRenderer $renderer, RouterInterface $router, Configuration $config)
    {
        $renderer->addPath(__DIR__ . '/views', 'social');

        $router->import(__DIR__ . '/resources/routes.yml', [
            'host' => sprintf(
                '%s.%s',
                $config->get('social.host', 'social'),
                $config->get('app_host', 'localhost')
            ),
            '_middlewares' => [AuthenticationMiddleware::class]
        ]);
    }

    /**
     * @param Configuration $configuration
     * @return mixed|void
     */
    public function configure(Configuration $configuration)
    {
        $configuration->set('social.host', 'social');
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'social_network';
    }
}
