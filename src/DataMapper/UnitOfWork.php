<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Persistence\DatabasePersister;
use Simplex\DataMapper\Persistence\PersisterInterface;
use Simplex\DataMapper\Proxy\ProxyFactory;

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
     * @return object|null
     */
    public function get(string $entityClass, $id): ?object
    {
        $mapper = $this->em->getMapperFor($entityClass);
        $meta = $mapper->getMetadata();
        $persister = $this->getPersister($entityClass);

        // Loads parent result first
        $result = $persister->load([
            $meta->getIdentifier() => $id
        ]);

        if (!$result) {
            return null;
        }

        $entity = $mapper->createEntity($result);
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
        $updates = $this->entityStates[self::STATE_MANAGED];
        $removes = array_values($this->entityStates[self::STATE_REMOVED]);

        if (empty($inserts) && empty($updates) && empty($removes)) {
            return;
        }

        foreach ($inserts as $class => $entities) {
            $persister = $this->getPersister($class);
            $mapper = $this->em->getMapperFor($class);
            foreach ($entities as $entity) {
                $entity = $mapper->prePersist($entity);
                $persister->addInsert($entity);
            }
            $persister->performInsert();
        }

        foreach ($updates as $entity) {
            $class = get_class($entity);
            $persister = $this->getPersister($class);
            $mapper = $this->em->getMapperFor($class);
            $changes = $this->getChangeSet($entity);
            if (!empty($changes)) {
                $entity = $mapper->prePersist($entity);
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
            $mapper = $this->em->getMapperFor($className);
            $meta = $mapper->getMetadata();
            $persister = $meta->customPersister() ?? DatabasePersister::class;
            $this->persisters[$className] = new $persister(
                $this->em,
                $mapper
            );
        }

        return $this->persisters[$className];
    }

    /**
     * Gets changed part of entities
     *
     * @param object $entity
     * @return array
     */
    protected function getChangeSet(object $entity): array
    {
        $mapper = $this->em->getMapperFor(\get_class($entity));
        $data = $mapper->extract($entity);
        $original = $mapper->extract($this->originalEntities[spl_object_hash($entity)] ?? new \stdClass());
        $changes = [];

        foreach ($data as $key => $value) {
            if (!isset($original[$key]) || ($value !== $original[$key])) {
                $changes[$key] = $value;
            }
        }

        return $changes;
    }
}
