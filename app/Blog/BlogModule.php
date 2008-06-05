<?php

namespace App\Blog;

use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use App\Blog\Action\ShowAction;


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
        $router->import(__DIR__.'/routes.yml', 'blog');
        $renderer->addPath(__DIR__.'/views', 'blog');
    }

}