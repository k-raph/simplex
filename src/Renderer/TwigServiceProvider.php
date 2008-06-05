<?php

namespace Simplex\Renderer;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class TwigServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    /**
     * {@inheritDoc}
     */
    protected $provides = [
        'twig',
        'renderer'
    ];

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $loader = new FilesystemLoader();
        $twig = new Environment($loader);

        $this->container->share(\Twig_LoaderInterface::class, $loader);
        $this->container->share(Environment::class, $twig);
        $this->container->share(TwigRenderer::class, function() {
            return new TwigRenderer(
                $this->container->get(Environment::class),
                $this->container->get(\Twig_LoaderInterface::class)
            );
        });
    }

    public function register(){}
   
}