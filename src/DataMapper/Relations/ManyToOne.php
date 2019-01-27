<?php

namespace Simplex\DataMapper\Relations;

use Simplex\DataMapper\EntityManager;

class ManyToOne implements RelationInterface
{
    protected $owner;

    protected $target;

    protected $ownerField;

    protected $targetField;

    public function __construct(string $owner, string $target, string $targetField, string $ownerField = 'id')
    {
        $this->owner = $owner;
        $this->target = $target;
        $this->targetField = $targetField;
        $this->ownerField = $ownerField;
    }

    /**
     * Loads all the relations for givens entities
     *
     * @param EntityManager $em
     * @param array $entities
     * @param array $fields
     * @return array
     * @throws \Throwable
     */
    public function load(EntityManager $em, array $entities, array $fields): array
    {
        $ownerMapper = $em->getMapperFor($this->getOwner());
        $targetMapper = $em->getMapperFor($this->getTarget());
        $field = $ownerMapper->getMetadata()->getName($this->getOwnerField());

        $ids = array_map(function ($entity) use ($field, $ownerMapper) {
            return $ownerMapper->getField($entity, $field);
        }, $entities);

        $query = $em->getConnection()->getQueryBuilder();
        $fields = array_merge([$this->getTargetField()], $fields);
        $result = $query
            ->table($targetMapper->getMetadata()->getTableName())
            ->addSelect($fields)
            ->whereIn($this->getTargetField(), $ids)
            ->get();

        $result = array_map([$targetMapper, 'createEntity'], $result);
        $sorted = [];

        foreach ($ids as $id) {
            $sorted[$id] = current(array_filter($result, function ($entity) use ($ownerMapper, $targetMapper, $id, $field) {
                return $id === $targetMapper->getField($entity, $this->getTargetField());
            }));
        }

        return $sorted;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getOwnerField(): string
    {
        return $this->ownerField;
    }

    public function getTargetField(): string
    {
        return $this->targetField;
    }

    /**
     * Assign loaded relations to given sources
     *
     * @param EntityManager $manager
     * @param string $name
     * @param array $sources
     * @param array $targets
     * @return array
     */
    public function assign(EntityManager $manager, string $name, array $sources, array $targets): array
    {
        $mapper = $manager->getMapperFor($this->getOwner());

        $field = $mapper->getMetadata()->getName($this->getOwnerField());

        $ids = array_keys($targets);
        foreach ($sources as &$entity) {
            $key = $mapper->getField($entity, $field);
            if (in_array($key, $ids)) {
                $result = [$mapper->getMetadata()->getSQLName($name) => $targets[$key]];
                $mapper->hydrate($entity, $result);
            }
        }

        return $sources;
    }
}
