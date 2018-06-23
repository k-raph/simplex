<?php

namespace Simplex\Event;

use Symfony\Component\HttpFoundation\Response;


class ViewEvent extends KernelEvent
{

    /**
     * Result returned by controller
     *
     * @var mixed
     */
    private $controllerResult;

    /**
     * Constructor
     *
     * @param mixed $response
     */
    public function __construct($response)
    {
        $this->name = 'kernel.view';
        $this->controllerResult = $response;
    }

    /**
     * Get provided controller result
     *
     * @return mixed
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }
}