<?php

namespace Simplex\Renderer;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
        $this->container->add(
            TwigRenderer::class,
            function () {
                $loader = new FilesystemLoader();
                $twig = new Environment($loader);

                $config = $this->container
                    ->get(Configuration::class)
                    ->get('twig');

                $loader->addPath($config['path']);

                foreach ($config['extensions'] ?? [] as $extension) {
                    $twig->addExtension($this->container->get($extension));
                }

                return new TwigRenderer($twig, $loader);
        });
    }

}