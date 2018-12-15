<?php

namespace Simplex\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use SplQueue;
use Simplex\Http\RequestHandlerInterface;

class Pipeline implements RequestHandlerInterface, MiddlewareInterface
{

    /**
     * @var SplQueue
     */
    private $stack;

    /**
     * @var RequestHandlerInterface
     */
    private $finalHandler;

    /**
     * Constructor
     */
    public function __construct(?MiddlewareInterface $finalHandler = null)
    {
        $this->stack = new SplQueue();
        $this->finalHandler = $finalHandler ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request): Response
    {
        if ($this->stack->isEmpty()) {
            if ($this->finalHandler) {
                return $this->finalHandler->handle($request);
            }

            throw new \RuntimeException('There is no middleware registered in the pipeline.');
        }

        $middleware = $this->stack->dequeue();       
        return $middleware->process($request, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->finalHandler = $handler;
        return $this->handle($request);
    }

    /**
     * Add a middleware to the stack
     *
     * @param MiddlewareInterface $middleware
     * @return self
     */
    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->stack->enqueue($middleware);
        return $this;
    }
}