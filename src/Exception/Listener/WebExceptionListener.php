<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/06/2019
 * Time: 18:14
 */

namespace Simplex\Exception\Listener;

use Simplex\Database\Exceptions\ResourceNotFoundException as DatabaseResourceNotFoundException;
use Simplex\EventManager\EventManagerInterface;
use Simplex\Exception\Event\HttpExceptionEvent;
use Simplex\Exception\Event\KernelExceptionEvent;
use Simplex\Security\Csrf\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class WebExceptionListener
{

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * WebExceptionListener constructor.
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param KernelExceptionEvent $event
     * @return KernelExceptionEvent
     * @throws \Exception
     */
    public function handle(KernelExceptionEvent $event): KernelExceptionEvent
    {
        $exception = $event->getException();

        switch (true) {
            case $exception instanceof DatabaseResourceNotFoundException:
            case $exception instanceof ResourceNotFoundException:
                $httpEvent = $this->eventManager->dispatch(new HttpExceptionEvent($exception, 404, [
                    'title' => 'Page Not Found',
                    'content' => 'Sorry, the page you are looking for could not be found.'
                ]));
                if ($httpEvent->hasResponse()) {
                    $response = $httpEvent->getResponse();
                } else {
                    $response = new Response();
                    $response->setContent("<title>404 Not Found</title> <h1>Not Found</h1>" . $exception->getMessage());
                    $response->setStatusCode($exception->getCode());
                }
                break;
            case $exception instanceof TokenMismatchException:
                $httpEvent = $this->eventManager->dispatch(new HttpExceptionEvent($exception, 419, [
                    'title' => 'Session expired',
                    'content' => 'Sorry, your session has expired. Please refresh and try again.'
                ]));
                if ($httpEvent->hasResponse()) {
                    $response = $httpEvent->getResponse();
                } else {
                    $response = new Response();
                    $response->setContent("<title>419 Session Expired</title> <h1>Sorry, your session has expired.</h1>" . $exception->getMessage());
                    $response->setStatusCode($exception->getCode());
                }
                break;
            case $exception instanceof MethodNotAllowedException:
                $response = new Response(
                    $exception->getMessage(),
                    405,
                    ['Allow' => implode(', ', $exception->getAllowedMethods())]
                );
                break;
            default:
                throw $exception;
        }

        $event->setResponse($response);
        return $event;
    }
}
