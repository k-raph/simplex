<?php

namespace Simplex\DataMapper\Repository;

use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMapper;

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
     * @var string
     */
    protected $className;

    protected $relations = [];

    public function __construct(EntityManager $manager, EntityMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->className = $mapper->getMetadata()->getEntityClass();
        $this->em = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id): ?object
    {
        $entity = $this->em->find($this->className, $id);
        return $entity
            ? current($this->checkRelations([$entity]))
            : null;
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
    public function findBy(array $criteria, ?string $orderBy = 'DESC', ?int $limit = null, int $offset = 0): array
    {
        $uow = $this->em->getUnitOfWork();
        $persister = $uow->getPersister($this->className);
        $result = $persister->loadAll($criteria, $orderBy, $limit, $offset);
        
        $result = array_map(function (array $data) {
            $entity = $this->mapper->createEntity($data);
            return $entity;
        }, $result);

        $result = $this->checkRelations($result);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria): ?object
    {
        return $this->findBy($criteria, null, 1, null);
    }

    /**
     * Get managed entity class name
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Check if relations need to be loaded
     *
     * @param array $entities
     * @return array
     */
    private function checkRelations(array $entities): array
    {
        if (empty($this->relations)) {
            return $entities;
        }

        $result = $this->mapper->loadRelations($entities, $this->relations);
        $this->relations = [];
        return $result;
    }

    /**
     * Add relations to be loaded
     *
     * @param string ...$relations
     * @return Repository
     */
    public function with(string ...$relations): self
    {
        $this->relations = array_merge($this->relations, $relations);
        return $this;
    }
}
