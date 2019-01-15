<?php

namespace Simplex\DataMapper\Repository;

use Simplex\DataMapper\Persistence\PersisterInterface;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Proxy\ProxyFactory;
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
            ? $this->checkRelations($entity)
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
            return $this->checkRelations($entity);
            return $entity;
        }, $result);

        $this->relations = [];
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

    private function checkRelations(object $entity)
    {
        if (empty($this->relations)) {
            return $entity;
        }

        return $this->mapper->loadRelations($entity, $this->relations);
    }

    public function with(string ...$relations): self
    {
        $this->relations = array_merge($this->relations, $relations);
        return $this;
    }
}
