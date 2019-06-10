<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:39
 */

namespace Simplex\Middleware;


use Simplex\Event\EventManagerInterface;
use Simplex\Exception\Event\ExceptionEvent;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * ErrorHandlerMiddleware constructor.
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Process an incoming HTTP Request and returns a Response
     *
     * @param Request $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $exception) {

            $event = new ExceptionEvent($exception, $request);
            $this->eventManager->emit('kernel.exception', [$event]);

            if (!$event->hasResponse()) {
                throw $exception;
            }

            return $event->getResponse();
        }
    }
}