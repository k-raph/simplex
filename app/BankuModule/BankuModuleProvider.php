<?php

namespace App\BankuModule;

use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;

class BankuModuleProvider extends AbstractModule
{

    /**
     * @var string
     */
    private $host;

    public function configure(Configuration $configuration)
    {
        //$configuration->load(__DIR__ . '/resources/config.yml', 'jobeet');
        $this->host = $configuration->get('app_host', 'localhost');
    }

    /**
     * @param RouteCollection $collection
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml', [
            'host' => 'banku.' . $this->host
        ]);
    }

    /**
     * @param TwigRenderer $renderer
     * @throws \Twig_Error_Loader
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'banku');
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'banku';
    }

    /**
     * @return string|null
     */
    public function getMigrationsConfig(): ?string
    {
        return __DIR__ . '/resources/db/phinx.yml';
    }
}
