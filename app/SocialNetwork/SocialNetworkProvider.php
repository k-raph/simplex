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
use Simplex\Routing\RouteCollection;

class SocialNetworkProvider extends AbstractModule
{

    /**
     * @var string
     */
    private $appHost;

    /**
     * @var string
     */
    private $host;

    /**
     * @param Configuration $configuration
     * @return mixed|void
     */
    public function configure(Configuration $configuration)
    {
        $configuration->set('social.host', 'social');

        $this->host = $configuration->get('social.host', 'social');
        $this->appHost = $configuration->get('app_host', 'localhost');
    }

    /**
     * @param RouteCollection $collection
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml', [
            'host' => sprintf('%s.%s', $this->host, $this->appHost),
            '_middlewares' => [AuthenticationMiddleware::class]
        ]);
    }

    /**
     * @param TwigRenderer $renderer
     * @throws \Twig_Error_Loader
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'social');
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
