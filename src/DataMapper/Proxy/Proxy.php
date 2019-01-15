<?php

namespace Simplex\DataMapper\Proxy;

use ReflectionProperty;
use Simplex\DataMapper\Mapping\EntityMetadata;

class Proxy
{

    /**
     * @var object
     */
    protected $instance;

    /**
     * @var ReflectionProperty[]
     */
    protected $properties = [];

    /**
     * @var array
     */
    protected $fieldMaps;

    /**
     * Proxy class to encapsulate an entity object
     *
     * @param object $instance
     */
    public function __construct(object $instance)
    {
        $this->instance = $instance;
        foreach ((new \ReflectionClass($instance))->getProperties() as $property) {
            $property->setAccessible(true);
            $this->properties[$property->getName()] = $property;
        };
    }

    /**
     * Reveals wrapped object
     *
     * @return object
     */
    public function reveal(): object
    {
        return $this->instance;
    }

    /**
     * Extracts object properties to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $values = array_map(function (ReflectionProperty $property) {
            return $property->getValue($this->instance);
        }, $this->properties);

        return array_combine(
            array_keys($this->properties),
            $values
        );
    }

    /**
     * Hydrates the entity with provided values
     *
     * @param array $data
     * @return void
     */
    public function hydrate(array $data)
    {
        foreach ($data as $name => $value) {
            if (!is_string($name) || !isset($this->properties[$name])) {
                continue;
            }

            $this->properties[$name]->setValue($this->instance, $value);
        }
    }

    /**
     * Retrieve a property value
     *
     * @param string $name
     * @return mixed
     */
    public function getField(string $name)
    {
        return isset($this->properties[$name])
            ? $this->properties[$name]->getValue($this->instance)
            : null;
    }

    public function setTarget(object $target)
    {
        $this->instance = $target;
    }
}
