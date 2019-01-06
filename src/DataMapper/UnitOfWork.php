<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Persistence\PersisterInterface;
use Simplex\DataMapper\Proxy\Proxy;
use Simplex\DataMapper\Proxy\ProxyFactory;
use Simplex\DataMapper\Persistence\DatabasePersister;
use Symfony\Component\Config\Definition\Exception\Exception;

class UnitOfWork
{
    const STATE_NEW = 'new';

    const STATE_MANAGED = 'managed';

    const STATE_REMOVED = 'removed';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var PersisterInterface[]
     */
    protected $persisters = [];

    /**
     * Container of entities' states
     *
     * @var array
     */
    protected $entityStates = [
        self::STATE_NEW => [],
        self::STATE_MANAGED => [],
        self::STATE_REMOVED => []
    ];

    /**
     * @var array
     */
    protected $originalEntities = [];

    public function __construct(EntityManager $manager, ProxyFactory $proxies)
    {
        $this->em = $manager;
        $this->proxyFactory = $proxies;
    }

    /**
     * Loads an entity by its identifier
     *
     * @param string $entityClass
     * @param mixed $id
     * @return void
     */
    public function get(string $entityClass, $id): ?object
    {
        if (isset($this->entityStates[self::STATE_MANAGED][$entityClass.$id])) {
            return $this->entityStates[self::STATE_MANAGED][$entityClass.$id];
        }

        $meta = $this->em->getMetadataFor($entityClass);
        $persister = $this->getPersister($entityClass);
        
        $result = $persister->load([
            $meta->getIdentifier() => $id
        ]);

        if (!$result) {
            return null;
        }

        $entity = $this->createEntity($entityClass, $result);
        $uid = spl_object_hash($entity);

        $this->entityStates[self::STATE_MANAGED][$uid] = $entity;
        $this->originalEntities[$uid] = clone $entity;

        return $entity;
    }

    /**
     * Sets an entity as persistent
     *
     * @param object $entity
     * @return void
     */
    public function persist(object $entity)
    {
        $uid = spl_object_hash($entity);

        if (isset($this->entityStates[self::STATE_MANAGED][$uid])) {
            $this->entityStates[self::STATE_MANAGED][$uid] = $entity;
        } else {
            $class = get_class($entity);
            $this->entityStates[self::STATE_NEW][$class][$uid] = $entity;
        }
    }

    /**
     * Removes an entity from managed ones
     *
     * @param object $entity
     * @return void
     */
    public function remove(object $entity)
    {
        $uid = spl_object_hash($entity);
        if (isset($this->entityStates[self::STATE_MANAGED][$uid])) {
            unset($this->entityStates[self::STATE_MANAGED][$uid]);
            $this->entityStates[self::STATE_REMOVED][$uid] = $entity;
        }
    }

    /**
     * Commit all the changes to persisters
     *
     * @return void
     */
    public function commit()
    {
        $inserts = $this->entityStates[self::STATE_NEW];
        $updates = array_values($this->entityStates[self::STATE_MANAGED]);
        $removes = array_values($this->entityStates[self::STATE_REMOVED]);

        if (empty($inserts) && empty($updates) && empty($removes)) {
            return;
        }

        foreach ($inserts as $class => $entities) {
            $persister = $this->getPersister($class);
            foreach ($entities as $entity) {
                $persister->addInsert($entity);
            }
            $persister->performInsert();
        }

        foreach ($updates as $entity) {
            $persister = $this->getPersister(get_class($entity));
            $changes = $this->getChangeSet($entity);
            if (!empty($changes)) {
                $persister->update($entity, $changes);
            }
        }

        foreach ($removes as $entity) {
            $persister = $this->getPersister(get_class($entity));
            $persister->delete($entity);
        }

        $this->entityStates[self::STATE_NEW] =
        $this->entityStates[self::STATE_MANAGED] =
        $this->entityStates[self::STATE_REMOVED] = [];
    }

    /**
     * Gets given entity class persister
     *
     * @param string $className
     * @return PersisterInterface
     */
    public function getPersister(string $className): PersisterInterface
    {
        if (!isset($this->persisters[$className])) {
            $meta = $this->em->getMetadataFor($className);
            $persister = $meta->customPersister() ?? DatabasePersister::class;
            $this->persisters[$className] = new $persister(
                $this->em,
                $meta
            );
        }

        return $this->persisters[$className];
    }

    /**
     * Extract values from given entity object
     *
     * @param object $entity
     * @return array
     */
    public function extract(object $entity): array
    {
        return $this->proxify($entity)->toPersistableArray();
    }

    /**
     * Retrieve a proxy wrapped around given object
     *
     * @param object $entity
     * @return Proxy|null
     */
    public function proxify(object $entity): ?Proxy
    {
        return $this->proxyFactory->wrap($entity);
    }

    /**
     * Gets changed part of entities
     *
     * @param object $entity
     * @return array
     */
    protected function getChangeSet(object $entity): array
    {
        $data = $this->extract($entity);
        $original = $this->extract($this->originalEntities[spl_object_hash($entity)]);
        $changes = [];

        foreach ($data as $key => $value) {
            if (!isset($original[$key]) || ($value !== $original[$key])) {
                $changes[$key] = $value;
            }
        }

        return $changes;
    }
    
    /**
     * Creates entities from an array
     *
     * @param array $data
     * @return object|null
     */
    public function createEntity(string $className, array $data = []): ?object
    {
        $proxy = $this->proxyFactory->create($className, $data);
        return $proxy->reveal();
    }
}
