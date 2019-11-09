<?php

namespace App\AskeetModule;

use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;
use Twig\TwigFilter;

class AskeetModuleProvider extends AbstractModule
{

    /**
     * @param RouteCollection $collection
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml');
    }

    /**
     * @param TwigRenderer $renderer
     * @throws \Twig_Error_Loader
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'askeet');
        $renderer->getEnv()->addFilter(new TwigFilter('ago', function ($date) {
            $date = $date instanceof \DateTime ? $date : new \DateTime($date);
            $interval = (new \DateTime())->diff($date);
            $maps = ['y' => 'years', 'm' => 'months', 'd' => 'days', 'h' => 'hours', 'i' => 'minutes', 's' => 'seconds'];
            $result = $date->format('Y-m-d H:i:s');
            foreach ($maps as $key => $name) {
                $value = $interval->$key;
                if ($value < 1) {
                    continue;
                }
                $name = $value == 1 ? substr($name, 0, -1) : $name;
                $result = "$value $name ago";
                break;
            }
            return $result;
        }));
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'askeet';
    }

    /**
     * @return string|null
     */
    public function getMigrationsConfig(): ?string
    {
        return __DIR__ . '/resources/migrations.yml';
    }
}