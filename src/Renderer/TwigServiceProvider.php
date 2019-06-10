<?php

namespace Simplex\Renderer;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
use Simplex\Event\EventManagerInterface;
use Simplex\Strategy\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
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
                    ->get('view.twig');

                $loader->addPath($config['path']);

                foreach ($config['extensions'] ?? [] as $extension) {
                    $twig->addExtension($this->container->get($extension));
                }

                return new TwigRenderer($twig, $loader);
        });

        $this->container->get(EventManagerInterface::class)
            ->on('kernel.http_exception', function (ExceptionEvent $event) {
                $response = new Response();
                $twig = $this->container->get(TwigRenderer::class);
                $response->setContent($twig->render('errors/4xx'));
                $response->setStatusCode($event->getException()->getCode());

                $event->setResponse($response);
            });
    }

}