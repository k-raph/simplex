<?php

namespace Simplex\Renderer;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Simplex\Renderer\TwigRenderer;

class TwigServiceProvider extends AbstractServiceProvider
{

    /**
     * {@inheritDoc}
     */
    protected $provides = [
        TwigRenderer::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->container->add(TwigRenderer::class, function() {    
            $loader = new FilesystemLoader();
            $twig = new Environment($loader);

            return new TwigRenderer($twig, $loader);
        });
    }
   
}