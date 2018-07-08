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

        $this->container->share(\Twig_LoaderInterface::class, FilesystemLoader::class);
        $this->container->share(Environment::class, $twig);
    }

    public function register(){}
   
}