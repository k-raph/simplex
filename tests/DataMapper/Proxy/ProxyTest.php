<?php

namespace Simplex\Tests\DataMapper\Proxy;

use PHPUnit\Framework\TestCase;
use Simplex\DataMapper\Proxy\Proxy;

class ProxyTest extends TestCase
{
    public function testReveal()
    {
        $object = new Foo();
        $proxy = new Proxy($object);

        $this->assertSame($object, $proxy->reveal());
        
        $object->setBar('bar');
        $this->assertSame($object, $proxy->reveal());
    }

    public function testToArrayWithoutMappings()
    {
        $object = new Foo();
        $proxy = new Proxy($object);

        $this->assertSame(['bar' => null], $proxy->toArray());
        
        $object->setBar('bar');
        $this->assertSame(['bar' => 'bar'], $proxy->toArray());
    }

    public function testToArrayWithFieldMapings()
    {
        $object = new Foo();
        $proxy = new Proxy($object, ['bar' => 'foo_bar']);

        $this->assertSame(['foo_bar' => null], $proxy->toArray());
        
        $object->setBar('bar');
        $object->name = 'foo';
        $this->assertSame(['foo_bar' => 'bar'], $proxy->toArray());
    }

    public function testHydrate()
    {
        $object = new Foo();
        $proxy = new Proxy($object);
        $proxy->hydrate(['bar' => 'foo_bar', 'name' => 'foo']);
        
        $this->assertEquals('foo_bar', $object->getBar());
        $this->assertSame(['bar' => 'foo_bar'], $proxy->toArray());
    }
}

class Foo
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
