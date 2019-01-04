<?php

namespace Simplex\DataMapper\Proxy;

use Simplex\DataMapper\Mapping\MetadataFactory;

class ProxyFactory
{

    /**
     * @var MetadataFactory
     */
    protected $metadatas;

    public function __construct(MetadataFactory $metaFactory)
    {
        $this->metadatas = $metaFactory;
    }

    /**
     * Create a proxy for given class using provided values
     *
     * @param string $class
     * @param array $values
     * @return Proxy
     */
    public function create(string $class, array $values): Proxy
    {
        $refl = new \ReflectionClass($class);
        $instance = $refl->newInstanceWithoutConstructor();
        $proxy = new Proxy($instance, $this->getMappings($class));
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
        $proxy = new Proxy($entity, $this->getMappings(get_class($entity)));

        return $proxy;
    }

    /**
     * Extract properties values from an object entity
     *
     * @param object $entity
     * @return array
     */
    protected function extract(object $entity): array
    {
        $data = [];
        foreach ((new \ReflectionClass($entity))->getProperties() as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($entity);
        }
        
        return $data;
    }

    /**
     * Get field mappings for provided class
     *
     * @param string $className
     * @return array
     */
    protected function getMappings(string $className): array
    {
        $metadata = $this->metadatas->getClassMetadata($className);

        return !$metadata
            ? []
            : array_combine(
            $metadata->getNames(),
            $metadata->getSQLNames()
        );
    }
}
