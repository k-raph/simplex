<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Repository\RepositoryInterface;
use Simplex\DataMapper\Repository\Factory as RepositoryFactory;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Mapping\MetadataFactory;

class EntityManager
{

    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var RepositoryFactory
     */
    protected $repositoryFactory;

    public function __construct(Configuration $config)
    {
        $this->metadataFactory = $config->getMetadataFactory();
        $this->repositoryFactory = $config->getRepositoryFactory();
    }

    /**
     * Get metadata for provided class
     *
     * @param string $className
     * @return EntityMetadata|null
     */
    public function getMetadataFor(string $className): ?EntityMetadata
    {
        return $this->metadataFactory->getClassMetadata($className);
    }

    /**
     * Find an entity by its primary key
     *
     * @param string $entityClass
     * @param mixed $key
     * @return object
     */
    public function find(string $entityClass, $key): object
    {
        return $this->getRepository($entityClass)->findOneBy([
            $this->getMetadataFor($entityClass)->getIdentifier() => $key
        ]);
    }

    /**
     * Get repository for entity class
     *
     * @param string $entityClass
     * @return RepositoryInterface
     */
    public function getRepository(string $entityClass): RepositoryInterface
    {
        return $this->repositoryFactory->getClassRepository($entityClass);
    }

    public function persist(object $entity)
    {
    }

    public function remove(object $entity)
    {
    }

    public function flush()
    {
    }
}
