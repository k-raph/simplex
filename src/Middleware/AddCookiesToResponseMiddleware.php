<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 12:41
 */

namespace Simplex\Middleware;

use Simplex\Http\CookieStorage;
use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCookiesToResponseMiddleware implements MiddlewareInterface
{

    /**
     * @var CookieStorage
     */
    private $storage;

    /**
     * AddCookiesToResponseMiddleware constructor.
     * @param CookieStorage $storage
     */
    public function __construct(CookieStorage $storage)
    {
        $this->storage = $storage;
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
        $response = $handler->handle($request);
        $cookies = $this->storage->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
