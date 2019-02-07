<?php

namespace Simplex\DataMapper\Persistence;

use Simplex\Database\Query\Builder;
use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMapper;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Proxy\Proxy;


class DatabasePersister implements PersisterInterface
{
    
    /**
     * @var Builder
     */
    protected $builder;

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

    /**
     * @var EntityMapper
     */
    private $mapper;

    public function __construct(EntityManager $manager, EntityMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->metadata = $mapper->getMetadata();
        $this->em = $manager;
        $this->builder = $manager->getConnection()
            ->getQueryBuilder()
            ->table($this->metadata->getTableName());
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $criteria): array
    {
        return $this->builder->where($criteria)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll(array $criteria, $orderBy = null, ?int $limit = null, ?int $offset = 0): array
    {
        /** @var Builder $query */
        $query = $this->builder->where($criteria);

        if ($orderBy && !in_array(strtoupper($orderBy), ['ASC', 'DESC'])) {
            $orderBy = is_array($orderBy) ? $orderBy : [$orderBy, 'DESC'];
            $query = $query->orderBy($orderBy[0], $orderBy[1]);
        }

        if ($limit) {
            $query = $query->limit($limit);
        }

        if ($offset) {
            $query = $query->offset($offset);
        }

        return $query->get();
    }

    /**
     * {@inheritDoc}
     */
    public function performInsert()
    {
        $this->em->getConnection()->transaction(function () {
            $input = array_values($this->entityInserts);
            $this->builder->insert($input);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function addInsert(object $entity)
    {
        $class = $this->metadata->getEntityClass();
        if ($entity instanceof $class) {
            $this->entityInserts[] = $this->mapper->extract($entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(object $entity, array $changes)
    {
        $where = $this->getWhere($entity);
        foreach ($changes as $name => $value) {
            if ($value instanceof \DateTime) {
                $changes[$name] = $value->format('Y-m-d H:i:s');
            }
        }

        $this->builder->where($where)->update($changes);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(object $entity)
    {
        $where = $this->getWhere($entity);

        $this->builder->where($where)->delete()->run();
    }

    /**
     * Gets where clause for efficient operation
     *
     * @param object $entity
     * @return array
     */
    protected function getWhere(object $entity): array
    {
        $id = $this->metadata->getIdentifier();
        $field = $this->metadata->getSQLName($id);
        $value = $this->mapper->getField($entity, $id);
        $where = [$field => $value];
        
        return $where;
    }

    /**
     * @return Builder
     */
    public function getQueryBuilder(?string $alias = null): Builder
    {
        return $this->builder->newQuery()->table($this->metadata->getTableName(), $alias);
    }
}
