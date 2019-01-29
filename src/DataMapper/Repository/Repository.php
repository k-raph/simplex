<?php

namespace Simplex\DataMapper\Repository;

use Simplex\Database\Query\Builder;
use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMapper;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Persistence\PersisterInterface;

class Repository implements RepositoryInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityMapper
     */
    protected $mapper;

    /**
     * @var EntityMetadata
     */
    private $metadata;

    /**
     * @var PersisterInterface
     */
    private $store;

    public function __construct(EntityManager $manager, EntityMapper $mapper)
    {
        $this->em = $manager;
        $this->mapper = $mapper;
        $this->metadata = $mapper->getMetadata();
        $this->store = $manager->getUnitOfWork()->getPersister($this->metadata->getEntityClass());
    }

    /**
     * {@inheritDoc}
     */
    public function find($id): ?object
    {
        return $this->em->find($this->metadata->getEntityClass(), $id);
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
        $result = $this->store->loadAll($criteria);

        $result = array_map(function (array $data) {
            $entity = $this->mapper->createEntity($data);
            return $entity;
        }, $result);

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
     * @return Builder
     */
    protected function query(?string $alias = null): Builder
    {
        return $this->store->getQueryBuilder($alias);
    }

    /**
     * @param $id
     * @param array $values
     * @return mixed|void
     */
    public function update($id, array $values)
    {
        $this->query()
            ->where([
                $this->metadata->getIdentifier() => $id
            ])
            ->update($values);
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function remove($id)
    {
        $this->query()
            ->where([
                $this->metadata->getIdentifier() => $id
            ])
            ->delete();
    }
}
