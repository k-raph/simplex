<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/04/2019
 * Time: 12:36
 */

namespace Simplex\Strategy\Listener;


use Simplex\Database\Exceptions\ResourceNotFoundException;
use Simplex\Strategy\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class JsonExceptionListener
{

    /**
     * Process an incoming HTTP Request and returns a Response
     *
     * @param ExceptionEvent $event
     * @return void
     */
    public function __invoke(ExceptionEvent $event): void
    {

        if (0 !== strpos($event->getRequest()->headers->get('Content-Type'), 'application/json')) {
            return;
        }

        $exception = $event->getException();

        switch (true) {
            case $exception instanceof \Symfony\Component\Routing\Exception\ResourceNotFoundException:
            case $exception instanceof ResourceNotFoundException:
                $response = new JsonResponse([
                    'code' => 404,
                    'message' => 'Resource not found.'
                ], 404);
                break;
            case $exception instanceof MethodNotAllowedException:
                $response = new JsonResponse([
                    'code' => 405,
                    'message' => $exception->getMessage()
                ],
                    405,
                    ['Allow' => implode(', ', $exception->getAllowedMethods())]
                );
                break;
            default:
                $response = new JsonResponse([
                    'code' => 500,
                    'message' => 'Sorry! An unexpected error where encountered.'
                ], 500);
        }

        $event->setResponse($response);
    }
}