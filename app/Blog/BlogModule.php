<?php

namespace App\Blog;

use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;

class BlogModule
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

}