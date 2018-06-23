<?php

namespace Simplex\Listener;

use League\Event\AbstractListener;
use League\Event\EventInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class ViewEventListener extends AbstractListener
{
    public function handle(EventInterface $event)
    {
        $response = $event->getControllerResult();
        if (
            is_string($response) || 
            (is_object($response) && method_exists($response, '__toString'))
        ) {
            $response = new Response((string)$response);
        } elseif (
            is_array($response) ||
            (is_object($response) && $object instanceof \ArrayAccess)
        ) {
            $response = new JsonResponse((array)$response);
        } else 
            throw new \LogicException(sprintf('Controller must return a string, an array or a Response object. "%s" given', gettype($response)));

        $event->setResponse($response);
        return $event;
    }
}