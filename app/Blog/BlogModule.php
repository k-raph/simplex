<?php

namespace App\Blog;

use App\Blog\Extension\TwigTextExtension;
use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;

class BlogModule extends AbstractModule
{

    /**
     * Constructor
     *
     * @param TwigRenderer $renderer
     * @param RouterInterface $router
     */
    public function __construct(TwigRenderer $renderer, RouterInterface $router, Configuration $configuration)
    {
        $configuration->load(__DIR__ . '/config/config.yml', 'blog');
        $router->import(__DIR__ . '/config/routes.yml', ['prefix' => 'blog']);

        $renderer->getEnv()->addExtension(new TwigTextExtension());
        $renderer->addPath(__DIR__.'/views', 'blog');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'blog';
    }
}