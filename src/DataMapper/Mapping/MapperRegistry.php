<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 23:57
 */

namespace Simplex\DataMapper\Mapping;

class MapperRegistry
{

    private $mappings = [];

    /**
     * @var callable
     */
    private $resolver;

    /**
     * MapperRegistry constructor.
     */
    public function __construct()
    {
        $this->resolver = function (string $mapper) {
            return new $mapper();
        };
    }

    /**
     * Register an entity class against a mapper class
     *
     * @param string $entityClass
     * @param string $mapperClass
     */
    public function register(string $entityClass, string $mapperClass)
    {
        $this->mappings[$entityClass] = $mapperClass;
    }

    /**
     * Sets mapper instantiation resolver
     *
     * @param callable $resolver
     */
    public function setResolver(callable $resolver): void
    {
        $this->resolver = $resolver;
    }

    /**
     * Resolve given class mapper
     *
     * @param string $entityClass
     * @return mixed
     */
    public function resolve(string $entityClass)
    {
        if (!isset($this->mappings[$entityClass])) {
            throw new \OutOfBoundsException(sprintf('There is no mapper registered for entity class %s', $entityClass));
        }

        return call_user_func($this->resolver, $this->mappings[$entityClass]);
    }
}
