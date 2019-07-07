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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class WebExceptionListener
{

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

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
                $httpEvent = $this->eventManager->dispatch(new HttpExceptionEvent($exception, 404));
                if ($httpEvent->hasResponse()) {
                    $response = $httpEvent->getResponse();
                } else {
                    $response = new Response();
                    $response->setContent("<title>404 Not Found</title> <h1>Not Found</h1>" . $exception->getMessage());
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