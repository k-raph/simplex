<?php

namespace Simplex\Event;

use League\Event\Event;
use Symfony\Component\HttpFoundation\Response;


abstract class KernelEvent extends Event
{

    /**
     * Response instance
     *
     * @var Response
     */
    protected $response;

    /**
     * Setter
     *
     * @param Response $response
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();
    }

    /**
     * Wether a response was produced
     *
     * @return boolean
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }

    /**
     * Get the reeponse
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}