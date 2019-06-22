<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22/06/2019
 * Time: 20:50
 */

namespace Simplex\Exception\Event;


use Symfony\Component\HttpFoundation\Response;

class HttpExceptionEvent
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var int
     */
    private $statusCode;

    public function __construct(\Exception $exception, int $statusCode)
    {
        $this->exception = $exception;
        $this->statusCode = $statusCode;
    }

    /**
     * @return Response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * Checks response
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return null !== $this->response;
    }

    /**
     * @return \Exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

}