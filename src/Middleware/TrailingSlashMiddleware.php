<?php

namespace Simplex\Middleware;

use Simplex\Http\MiddlewareInterface;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TrailingSlashMiddleware implements MiddlewareInterface
{

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $uri = $request->getPathInfo();
        if ($uri != '/' && $uri[strlen($uri)-1] === '/') {
            return new RedirectResponse(substr($uri, 0, strlen($uri)-1));
        }

        return $handler->handle($request);
    }
}