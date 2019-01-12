<?php

namespace Simplex\DataMapper\Persistence;

use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMetadata;

class ArrayPersister implements PersisterInterface
{
    /**
     * Entity store
     *
     * @var object[]
     */
    protected $store = [];

    /**
     * @var EntityMetadata
     */
    protected $metadata;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Proxies to entity to insert
     *
     * @var Proxy[]
     */
    protected $entityInserts = [];

    protected $index = 1;

    public function __construct(EntityManager $manager, EntityMetadata $metadata)
    {
        $this->metadata = $metadata;
        $this->em = $manager;
    }

    /**
     * Loads an entry matching given criteria
     *
     * @param array $criteria
     * @return mixed
     */
    public function load(array $criteria): array
    {
        return $this->loadAll($criteria, null, 1)[0] ?? [];
    }

    /**
     * Loads all entries matching given filters
     *
     * @param array $criteria
     * @param string|null $orderBy
     * @param integer|null $limit
     * @param integer|null $offset
     * @return array
     */
    public function loadAll(array $criteria, ?string $orderBy = 'DESC', ?int $limit = null, ?int $offset = 0): array
    {
        /**
         * Filter stored item by given criteria
         */
        $filtered = array_filter(
            $this->store,
            function (object $entity) use ($criteria) {
                if (empty($criteria)) {
                    return true;
                }
                
                $data = $this->em->getUnitOfWork()->proxify($entity)->toArray();
                $id = $this->metadata->getIdentifier();
                return $data[$id] == $criteria[$id];
            }
        );

        if ($limit) {
            $filtered = array_slice($filtered, $offset, $limit);
        }

        $filtered = array_map(function (object $entity) {
            return $this->em->getUnitOfWork()->proxify($entity)->toPersistableArray();
        }, $filtered);

        return array_values($filtered);
    }
    
    /**
     * Adds an object to insert to store
     *
     * @param object $entity
     * @return void
     */
    public function addInsert(object $entity)
    {
        $class = $this->metadata->getEntityClass();

        if ($entity instanceof $class) {
            $this->em->getUnitOfWork()->proxify($entity)->hydrate([$this->metadata->getIdentifier() => $this->index]);
            $this->store[$this->index] = $entity;
            $this->index++;
        }
    }

    /**
     * Executes insert operations
     *
     * @return void
     */
    public function performInsert()
    {
        return true;
    }

    /**
     * Performs entity update
     *
     * @return void
     */
    public function update(object $entity, array $changes)
    {
        $class = $this->metadata->getEntityClass();
        $proxy = $this->em->getUnitOfWork()->proxify($entity);
        $id = $proxy->getField($this->metadata->getIdentifier());
        $entity = $this->store[$id] ?? null;

        if ($entity && $entity instanceof $class) {
            $proxy->hydrate($changes);
            $this->store[$id] = $proxy->reveal();
        }
    }

    /**
     * Performs entity deletion
     *
     * @param object $entity
     * @return void
     */
    public function delete(object $entity)
    {
        $class = $this->metadata->getEntityClass();
        $proxy = $this->em->getUnitOfWork()->proxify($entity);
        $id = $proxy->getField($this->metadata->getIdentifier());

        if (isset($this->store[$id])) {
            unset($this->store[$id]);
        }
    }
}
