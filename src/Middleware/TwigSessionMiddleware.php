<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 20:24
 */

namespace Simplex\Middleware;


use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TwigSessionMiddleware implements MiddlewareInterface
{

    /**
     * @var TwigRenderer
     */
    private $renderer;

    /**
     * TwigSessionMiddleware constructor.
     * @param TwigRenderer $renderer
     */
    public function __construct(TwigRenderer $renderer)
    {
        $this->renderer = $renderer;
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
        if ($request->hasSession()) {
            $env = $this->renderer->getEnv();
            $session = $request->getSession();
            $flash = $session->getFlashBag();

            $env->addGlobal('session', $session);
            $env->addGlobal('flash', $flash);
            $env->addGlobal('errors', new ParameterBag($flash->get('errors', [])));
        }

        return $handler->handle($request);
    }
}