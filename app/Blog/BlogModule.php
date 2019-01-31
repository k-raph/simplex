<?php

namespace App\Blog;

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
    public function __construct(TwigRenderer $renderer, RouterInterface $router)
    {
        $router->import(__DIR__.'/routes.yml', ['prefix' => 'blog']);
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