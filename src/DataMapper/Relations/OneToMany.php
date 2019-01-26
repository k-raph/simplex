<?php

namespace Simplex\DataMapper\Relations;

use Simplex\DataMapper\EntityManager;

class OneToMany implements RelationInterface
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
     * Loads all the relations for givens entities
     *
     * @param EntityManager $em
     * @param array $entities
     * @return array
     * @throws \Throwable
     */
    public function load(EntityManager $em, array $entities): array
    {
        $mapper = $em->getMapperFor($this->getTarget());

        $criteria = [
            $this->getOwnerField() => array_map(function ($entity) use ($mapper) {
                return $mapper->getField($entity, $this->getOwnerField());
            }, $entities)
        ];

        $meta = $mapper->getMetadata();

        $query = $em->getConnection()->getQueryBuilder();
        $result = $query
            ->table($meta->getTableName())
            ->whereIn($this->getTargetField(), $criteria[$this->getOwnerField()] ?? [])
            ->get();

        $result = array_map(function ($data) use ($mapper) {
            return $mapper->createEntity($data);
        }, $result);

        return $result;
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
        $targetMapper = $manager
            ->getMapperFor($this->getTarget());
        $field = $targetMapper->getMetadata()->getName($this->getTargetField());

        foreach ($sources as &$entity) {
            $result = array_filter($targets, function ($related) use ($targetMapper, $field, $entity, $mapper) {
                return $mapper->getField($entity, 'id') === $targetMapper->getField($related, $field);
            });

            // Used array_values to adjust result index in result
            $result = [$name => array_values($result)];
            $mapper->hydrate($entity, $result);
        }

        return $sources;
    }
}
