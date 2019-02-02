<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02/02/2019
 * Time: 08:42
 */

namespace Simplex\DataMapper;

use Simplex\Database\Query\Builder;
use Simplex\DataMapper\Mapping\EntityMapper;

class QueryBuilder extends Builder
{

    /**
     * @var EntityMapper
     */
    protected $mapper;
    /**
     * @var EntityManager
     */
    protected $manager;
    /**
     * @var Mapping\EntityMetadata
     */
    private $metadata;

    /**
     * @var UnitOfWork
     */
    private $uow;

    /**
     * QueryBuilder constructor.
     *
     * @param EntityManager $manager
     * @param EntityMapper $mapper
     */
    public function __construct(EntityManager $manager, EntityMapper $mapper)
    {
        parent::__construct($manager->getConnection());
        $this->manager = $manager;
        $this->mapper = $mapper;
        $this->metadata = $mapper->getMetadata();
        $this->uow = $manager->getUnitOfWork();
    }

    /**
     * {@inheritdoc}
     */
    public function get(): array
    {
        $result = parent::get();

        return array_map([$this->mapper, 'createEntity'], $result);
    }

    /**
     * Finds by identifier
     *
     * @param $id
     * @return object|null
     */
    public function find($id): ?object
    {
        return $this->manager->find($this->metadata->getEntityClass(), $id);
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        $result = parent::first();

        if ($result) {
            $result = $this->mapper->createEntity($result);
            $this->uow->setManaged($result);

            return $result;
        }

        return null;
    }

    /**
     * @return EntityManager
     */
    public function getManager(): EntityManager
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function newQuery(?string $alias = null): Builder
    {
        return (new static($this->manager, $this->mapper))
            ->table($this->metadata->getTableName(), $alias);
    }

    /**
     * Update an entity
     *
     * @param object $entity
     * @return int
     */
    public function update($entity): int
    {
        /** @var object $entity */
        if ($this->isSupported($entity)) {
            $changes = $this->uow->getChangeSet($entity);
            if (!empty($changes)) {
                return $this->where($this->getId($entity))
                    ->update($changes);
            }
        } elseif (is_array($entity)) {
            /** @var array $entity */
            return parent::update($entity);
        }

        return 0;
    }

    /**
     * Checks wether given object is supported
     *
     * @param object $entity
     * @return bool
     */
    protected function isSupported($entity): bool
    {
        return is_object($entity) && is_a($entity, $this->metadata->getEntityClass());
    }

    /**
     * Gets primary key from an entity
     *
     * @param object $entity
     * @return array
     */
    protected function getId(object $entity): array
    {
        $pk = $this->metadata->getIdentifier();
        $value = $this->mapper->getField($entity, $pk);

        return [$pk => $value];
    }

    /**
     * Deletes an entity
     *
     * @param $entity
     * @return int
     */
    public function delete(): int
    {
        $args = func_get_args();
        $entity = reset($args);

        if ($entity && $this->isSupported($entity)) {
            return parent::where($this->getId($entity))->delete();
        }

        return 0;
    }
}