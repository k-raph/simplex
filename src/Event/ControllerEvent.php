<?php

namespace Simplex\Event;

use Symfony\Component\HttpFoundation\Request;


class ControllerEvent extends KernelEvent
{

    /**
     * Resolved controller
     *
     * @var mixed
     */
    protected $controller;

    /**
     * Route params
     *
     * @var array
     */
    protected $params;

    public function __construct($controller, Request $request)
    {
        $this->controller = $controller;
        $this->params = $request->attributes->get('_route_params');
        $this->name = 'kernel.controller';
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }
}