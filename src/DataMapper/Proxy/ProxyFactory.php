<?php

namespace Simplex\DataMapper\Proxy;

use Simplex\DataMapper\Mapping\MetadataFactory;

class ProxyFactory
{

    /**
     * Create a proxy for given class using provided values
     *
     * @param string $class
     * @param array $values
     * @return Proxy
     */
    public function create(string $class, array $values = []): Proxy
    {
        $refl = new \ReflectionClass($class);
        $instance = $refl->newInstanceWithoutConstructor();
        $proxy = new Proxy($instance);
        $proxy->hydrate($values);

        return $proxy;
    }

    /**
     * Wraps an object within a proxy
     *
     * @param object $entity
     * @return Proxy
     */
    public function wrap(object $entity): Proxy
    {
        $proxy = new Proxy($entity);

        return $proxy;
    }
}
