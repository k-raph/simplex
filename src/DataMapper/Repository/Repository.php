<?php

namespace Simplex\DataMapper\Repository;

use Simplex\DataMapper\QueryBuilder;

class Repository implements RepositoryInterface
{

    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * Repository constructor.
     * @param QueryBuilder $builder
     */
    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id): ?object
    {
        return $this->query()->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): array
    {
        return $this->findBy([]);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria): array
    {
        $result = $this->query()
            ->where($criteria)
            ->get();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria): ?object
    {
        return $this->query()
            ->where($criteria)
            ->first();
    }

    /**
     * @param string|null $alias
     * @return QueryBuilder
     */
    protected function query(?string $alias = null): QueryBuilder
    {
        return $this->builder->newQuery($alias);
    }

    /**
     * @param $id
     * @param array $values
     * @return mixed|void
     */
    public function update(object $entity)
    {
        $this->query()
            ->update($entity);
    }

    /**
     * @param object $entity
     * @return mixed|void
     * @throws \Throwable
     */
    public function remove(object $entity)
    {
        $this->query()->delete($entity);
    }
}
