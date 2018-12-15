<?php

namespace Simplex\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use SplQueue;

class Pipeline implements RequestHandlerInterface
{

    /**
     * @var SplQueue
     */
    private $stack;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stack = new SplQueue();
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request): Response
    {
        if ($this->stack->isEmpty()) {
            throw new \RuntimeException('There is no middleware registered in the pipeline.');
        }

        $middleware = $this->stack->dequeue();       
        return $middleware->process($request, $this);
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