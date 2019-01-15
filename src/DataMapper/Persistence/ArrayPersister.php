<?php

namespace Simplex\DataMapper\Persistence;

use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMapper;
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
     * @var EntityMapper
     */
    protected $mapper;

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

    public function __construct(EntityManager $manager, EntityMapper $mapper)
    {
        $this->em = $manager;
        $this->mapper = $mapper;
        $this->metadata = $mapper->getMetadata();
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
                
                $data = $this->mapper->extract($entity);
                $id = $this->metadata->getIdentifier();
                return $data[$id] == $criteria[$id];
            }
        );

        if ($limit) {
            $filtered = array_slice($filtered, $offset, $limit);
        }

        $filtered = array_map(function (object $entity) {
            return $this->mapper->extract($entity);
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
            $this->mapper->hydrate($entity, [$this->metadata->getIdentifier() => $this->index]);
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
        $id = $this->mapper->getField($entity, $this->metadata->getIdentifier());
        $entity = $this->store[$id] ?? null;

        if ($entity && $entity instanceof $class) {
            $this->mapper->hydrate($entity, $changes);
            $this->store[$id] = $entity;
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
        $id = $this->mapper->getField($entity, $this->metadata->getIdentifier());

        if (isset($this->store[$id])) {
            unset($this->store[$id]);
        }
    }
}
