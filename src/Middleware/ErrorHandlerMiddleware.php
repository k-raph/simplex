<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:39
 */

namespace Simplex\Middleware;


use Simplex\Database\Exceptions\ResourceNotFoundException;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * ErrorHandlerMiddleware constructor.
     * @param TwigRenderer $view
     */
    public function __construct(TwigRenderer $view)
    {
        $this->view = $view;
    }

    /**
     * Process an incoming HTTP Request and returns a Response
     *
     * @param Request $request
     * @param RequestHandlerInterface $handler
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $exception) {
            switch (true) {
                case $exception instanceof \Symfony\Component\Routing\Exception\ResourceNotFoundException:
                case $exception instanceof ResourceNotFoundException:
                    $response = new Response();
                    $response->setContent($this->view->render('errors/4xx'));
                    $response->setStatusCode(404);

                    return $response;
                case $exception instanceof MethodNotAllowedException:
                    return new Response(
                        $exception->getMessage(),
                        405,
                        ['Allow' => implode(', ', $exception->getAllowedMethods())]
                    );
                default:
                    throw $exception;
            }
        }
    }
}