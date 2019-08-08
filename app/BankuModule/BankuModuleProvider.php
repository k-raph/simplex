<?php

namespace App\BankuModule;

use Simplex\Database\DatabaseManager;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class BankuModuleProvider extends AbstractModule
{

    /**
     * @param RouteCollection $collection
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->get('/banku', function (DatabaseManager $manager) {
            $accounts = $manager->getDatabase('banku')
                ->query('SELECT * FROM accounts')
                ->fetchAll();

            return new JsonResponse($accounts);
        });
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
