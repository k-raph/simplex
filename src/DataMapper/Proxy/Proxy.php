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
     * @param array $mappings
     */
    public function __construct(object $instance, array $mappings = [])
    {
        $this->instance = $instance;
        foreach ((new \ReflectionClass($instance))->getProperties() as $property) {
            $property->setAccessible(true);
            $this->properties[$property->getName()] = $property;
        };

        $this->fieldMaps = !empty($mappings)
            ? $mappings
            : array_combine(
                array_keys($this->properties),
                array_keys($this->properties)
            );
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
     * Converts object properties to persistable array
     *
     * @return array
     */
    public function toPersistableArray(): array
    {
        $array = [];
        foreach ($this->properties as $name => $property) {
            $field = $this->fieldMaps[$name];
            $array[$field] = $property->getValue($this->instance);
        }

        return $array;
    }

    public function toArray()
    {
        return array_map(function (ReflectionProperty $property) {
            return $property->getValue($this->instance);
        }, $this->properties);
    }

    /**
     * Hydrates the entity with provided values
     *
     * @param array $data
     * @return void
     */
    public function hydrate(array $data)
    {
        $mappings = array_flip($this->fieldMaps);
        foreach ($data as $key => $value) {
            $name = $mappings[$key] ?? null;
            if (!$name) {
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
}
