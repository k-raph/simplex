<?php

namespace Simplex\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Simplex\Routing\SymfonyRouter;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Simplex\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class SymfonyRouterTest extends TestCase
{
    public function setUp()
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $this->router = new SymfonyRouter($loader);
        $this->router->get('/test', 'controller', 'test');
        $this->router->match('GET|POST', '/hello/{name}', 'controller', 'hello');
    }

    public function testDispatch()
    {
        $request = Request::create('/test');
        $result = $this->router->dispatch($request);
        $this->assertInstanceOf(Route::class, $result);
        $this->assertEquals('controller', $result->getCallback());
        $this->assertEquals([], $result->getParams());
        
    }
    
    public function testDispatchWithDynamicParts()
    {
        $request = Request::create('/hello/world');
        $result = $this->router->dispatch($request);
        $this->assertInstanceOf(Route::class, $result);
        $this->assertEquals('controller', $result->getCallback());
        $this->assertEquals(['name' => 'world'], $result->getParams());
    }

    public function testDispatchUndefinedRoute()
    {
        $request = Request::create('/undefined');
        $this->expectException(ResourceNotFoundException::class);
        $this->router->dispatch($request);
    }
}