<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/04/2019
 * Time: 12:36
 */

namespace Simplex\Middleware;


use Simplex\Database\Exceptions\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class JsonErrorHandler
{

    /**
     * Process an incoming HTTP Request and returns a Response
     *
     * @param \Exception $exception
     * @return JsonResponse
     */
    public function handle(\Exception $exception): JsonResponse
    {
        switch (true) {
            case $exception instanceof \Symfony\Component\Routing\Exception\ResourceNotFoundException:
            case $exception instanceof ResourceNotFoundException:
                return new JsonResponse([
                    'code' => 404,
                    'message' => 'Resource not found.'
                ], 404);
            case $exception instanceof MethodNotAllowedException:
                return new JsonResponse([
                    'code' => 405,
                    'message' => $exception->getMessage()
                ],
                    405,
                    ['Allow' => implode(', ', $exception->getAllowedMethods())]
                );
            default:
                return new JsonResponse([
                    'code' => 500,
                    'message' => 'Sorry! An unexpected error where encountered.'
                ], 500);
        }
    }
}