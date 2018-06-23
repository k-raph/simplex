<?php

namespace Simplex\Listener;

use Simplex\Event\RequestEvent;
use League\Event\AbstractListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use League\Event\EventInterface;


class TrailingSlashListener extends AbstractListener
{

    public function handle(EventInterface $event)
    {
        $request = $event->getRequest();
        $uri = $request->getPathInfo();
        if ($uri != '/' && $uri[strlen($uri)-1] === '/') {
            $event->setResponse(new RedirectResponse(substr($uri, 0, strlen($uri)-1)));
        }

        return $event;
    }
}