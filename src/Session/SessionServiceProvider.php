<?php

namespace Simplex\Session;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

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
        $flashBag = new FlashBag('_simplex_flashes');

        $session = new Session(null, null, $flashBag);
        $session->setName('_simplex');

        $this->container->add(FlashBagInterface::class, $flashBag);
        $this->container->add(SessionInterface::class, $session);
    }

}