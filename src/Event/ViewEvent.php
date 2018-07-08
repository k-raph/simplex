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
     * @param mixed $result
     */
    public function __construct($result)
    {
        $this->name = 'kernel.view';
        $this->controllerResult = $result;
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