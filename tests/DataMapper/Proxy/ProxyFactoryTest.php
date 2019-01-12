<?php

namespace Simplex\Tests\DataMapper\Proxy;

use PHPUnit\Framework\TestCase;
use Simplex\DataMapper\Proxy\ProxyFactory;
use Simplex\DataMapper\Proxy\Proxy;
use Simplex\DataMapper\Mapping\MetadataFactory;

class ProxyFactoryTest extends TestCase
{
    public function testCreateProxy()
    {
        $factory = new ProxyFactory(new MetadataFactory);
        $proxy = $factory->create(\stdClass::class, []);

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertInstanceOf(\stdClass::class, $proxy->reveal());
    }

    public function testWrap()
    {
        $factory = new ProxyFactory(new MetadataFactory);
        $mock = new \stdClass();
        $proxy = $factory->wrap($mock);

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertInstanceOf(\stdClass::class, $proxy->reveal());
        $this->assertSame($mock, $proxy->reveal());
    }

    public function testWrapClassWithPrivateProperties()
    {
        $factory = new ProxyFactory(new MetadataFactory);
        $mock = new FooBar();
        $mock->setBar('bobo');

        $proxy = $factory->wrap($mock);

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertInstanceOf(FooBar::class, $proxy->reveal());
        $this->assertSame($mock, $proxy->reveal());
        $this->assertEquals('bobo', $proxy->reveal()->getBar());
    }
}

class FooBar
{

    /**
     * @var string
     */
    private $bar;

    public function setBar(string $bar)
    {
        $this->bar = $bar;
    }

    public function getBar(): ?string
    {
        return $this->bar;
    }
}
