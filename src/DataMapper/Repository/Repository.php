<?php

namespace Simplex\DataMapper\Repository;

use Simplex\DataMapper\Persistence\PersisterInterface;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Proxy\ProxyFactory;
use Simplex\DataMapper\EntityManager;

class Repository implements RepositoryInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityMetadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $className;

    public function __construct(EntityManager $manager, EntityMetadata $metadata)
    {
        $this->metadata = $metadata;
        $this->className = $metadata->getEntityClass();
        $this->em = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id): ?object
    {
        return $this->em->find($this->className, $id);
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
    public function findBy(array $criteria, ?string $orderBy = 'DESC', ?int $limit = null, ?int $offset): array
    {
        $persister = $this->em->getUnitOfWork()->getPersister($this->className);
        return $persister->loadAll($criteria, $orderBy, $limit, $offset);
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
}
