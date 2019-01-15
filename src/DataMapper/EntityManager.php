<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Repository\RepositoryInterface;
use Simplex\DataMapper\Repository\Factory as RepositoryFactory;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Mapping\MetadataFactory;
use Simplex\Database\DatabaseInterface;
use Simplex\DataMapper\Proxy\ProxyFactory;
use UnexpectedValueException;
use Simplex\Tests\DataMapper\EntityManagerTest;
use Simplex\DataMapper\Mapping\EntityMapper;

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

    /**
     * @var EntityMapper[]
     */
    protected $mappers = [];

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
     * @return EntityMapper
     */
    public function getMapperFor(string $className): EntityMapper
    {
        if (!isset($this->mappers[$className])) {
            $meta = $this->metadataFactory->getClassMetadata($className);
            $this->mappers[$className] = new EntityMapper($meta, $this) ;
        }

        return $this->mappers[$className];
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
     * Gets proxy factory
     *
     * @return ProxyFactory
     */
    public function getProxyFactory(): ProxyFactory
    {
        return $this->proxyFactory;
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
