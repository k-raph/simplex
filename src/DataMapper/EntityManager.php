<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Repository\RepositoryInterface;
use Simplex\DataMapper\Repository\Factory as RepositoryFactory;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Mapping\MetadataFactory;
use Simplex\Database\DatabaseInterface;
use Simplex\DataMapper\Proxy\ProxyFactory;
use UnexpectedValueException;

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

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var DatabaseInterface
     */
    protected $connection;

    /**
     * Unit of work instance
     *
     * @var UnitOfWork
     */
    protected $uow;

    public function __construct(Configuration $config, DatabaseInterface $connection)
    {
        $this->connection = $connection;
        $this->repositoryFactory = new RepositoryFactory();
        
        $config->setup($this);
        $this->metadataFactory = $config->getMetadataFactory();

        $this->proxyFactory = new ProxyFactory($this->metadataFactory);
        $this->uow = new UnitOfWork($this, $this->proxyFactory);
    }

    /**
     * Get metadata for provided class
     *
     * @param string $className
     * @return EntityMetadata|null
     */
    public function getMetadataFor(string $className): ?EntityMetadata
    {
        $meta = $this->metadataFactory->getClassMetadata($className);
        
        if (!$meta) {
            throw new UnexpectedValueException(sprintf('Metadata for class %s not found', $className));
        }

        return $meta;
    }

    /**
     * Find an entity by its primary key
     *
     * @param string $entityClass
     * @param mixed $key
     * @return object
     */
    public function find(string $entityClass, $key)//: object
    {
        return $this->uow->get($entityClass, $key);
    }

    /**
     * Get repository for entity class
     *
     * @param string $entityClass
     * @return RepositoryInterface
     */
    public function getRepository(string $entityClass): RepositoryInterface
    {
        return $this->repositoryFactory->getRepository($this, $entityClass);
    }

    /**
     * Gets database connection
     *
     * @return DatabaseInterface
     */
    public function getConnection(): DatabaseInterface
    {
        return $this->connection;
    }

    /**
     * Gets the unit of work instance
     *
     * @return UnitOfWork
     */
    public function getUnitOfWork(): UnitOfWork
    {
        return $this->uow;
    }

    /**
     * Store an entity as persistent
     */
    public function persist(/*object*/ $entity)
    {
        $this->uow->persist($entity);
    }

    /**
     * Removes an entity from the manager
     */
    public function remove(/*object*/ $entity)
    {
        return $this->uow->remove($entity);
    }

    /**
     * Flushes the entity manager to commit the changes
     *
     * @return void
     */
    public function flush()
    {
        return $this->uow->commit();
    }
}
