<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/03/2019
 * Time: 11:23
 */

namespace Simplex\Routing\Middleware;


use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response;

interface StrategyInterface extends MiddlewareInterface
{

    public function add(MiddlewareInterface $middleware);

    public function handle($result): Response;
}