<?php

namespace Simplex\DataMapper\Persistence;

use Simplex\Database\Query\Builder;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Proxy\ProxyFactory;
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
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * Proxies to entity to insert
     *
     * @var Proxy[]
     */
    protected $entityInserts = [];

    public function __construct(Builder $builder, EntityMetadata $metadata, ProxyFactory $proxies)
    {
        $this->metadata = $metadata;
        $this->builder = $builder->table($metadata->getTableName());
        $this->proxyFactory = $proxies;
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $criteria)
    {
        return $this->builder->where($criteria)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll(array $criteria, $orderBy = null, ?int $limit = null, ?int $offset): array
    {
        /** @var Builder $query */
        $query = $this->builder->where($criteria);

        if ($orderBy) {
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
        $this->builder->transaction(function () {
            $input = array_map(function (Proxy $proxy) {
                return $proxy->toArray();
            }, $this->entityInserts);
            
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
            $this->entityInserts[] = $this->proxyFactory->wrap($entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(object $entity, array $changes)
    {
        $where = $this->getWhere($entity);

        $this->builder->where($where)->update($changes)->run();
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
        $proxy = $this->proxyFactory->wrap($entity);
        $id = $this->metadata->getIdentifier();
        $where = [$id => $proxy->getField($id)];

        return $where;
    }
}
