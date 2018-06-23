<?php

namespace Simplex\Event;

use Symfony\Component\HttpFoundation\Request;

class RequestEvent extends KernelEvent
{

    /**
     * Request
     *
     * @var Request
     */
    private $request;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->name = 'kernel.request';
    }

    /**
     * Get current request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}