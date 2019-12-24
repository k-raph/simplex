<?php

namespace Simplex\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionServiceProvider extends AbstractServiceProvider
{

    /**
     * {@inheritDoc}
     */
    protected $provides = [
        SessionInterface::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $host = $this->container->get(Configuration::class)->get('app_host', 'localhost');
        $flashBag = new FlashBag('_simplex_flashes');
        $storage = new NativeSessionStorage(['cookie_domain' => ".$host"]);

        $session = new Session($storage, null, $flashBag);
        $session->setName('_simplex');

        $this->container->add(FlashBagInterface::class, $flashBag);
        $this->container->add(SessionInterface::class, $session);
    }
}
