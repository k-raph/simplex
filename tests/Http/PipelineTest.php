<?php

namespace Simplex\Tests\Http;

use PHPUnit\Framework\TestCase;
use Simplex\Http\Pipeline;
use Simplex\Http\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response;
use Simplex\Http\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class PipelineTest extends TestCase
{

    /**
     * Pipeline instance
     *
     * @var Pipeline
     */
    private $pipeline;

    public function setUp()
    {
        $this->pipeline = new Pipeline();
        $this->pipeline
            ->pipe($this->decorate(function (Request $request, $handler) {
                $request->attributes->set('app', 'simplex');
                return $handler->handle($request);
            }))
            ->pipe($this->decorate(function (Request $request, $handler) {
                return new Response($request->attributes->get('app'));
            }));
    }

    /**
     * Decorate a callable into middleware interface class
     *
     * @param \Closure $callback
     * @return MiddlewareInterface
     */
    private function decorate(\Closure $callback): MiddlewareInterface
    {
        return new class($callback) implements MiddlewareInterface {
            
            /**
             * Decorated callback
             *
             * @var \Closure
             */
            protected $callback;

            public function __construct(\Closure $callback)
            {
                $this->callback = $callback;
            }

            /**
             * {@inheritDoc}
             */
            public function process(Request $request, RequestHandlerInterface $handler): Response
            {
                return call_user_func($this->callback, $request, $handler);
            }
        };
    }

    public function testHandle()
    {
        $request = Request::create('/test');
        $response = $this->pipeline->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertContains('simplex', $response->getContent());
    }

}
