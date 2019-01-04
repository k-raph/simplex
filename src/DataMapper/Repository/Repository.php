<?php

namespace Simplex\DataMapper\Repository;

use Simplex\DataMapper\Persistence\PersisterInterface;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Proxy\ProxyFactory;

class Repository implements RepositoryInterface
{

    /**
     * @var PersisterInterface
     */
    protected $persister;

    /**
     * @var EntityMetadata
     */

    protected $metadata;

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    public function __construct(EntityMetadata $metadata, PersisterInterface $persister, ProxyFactory $factory)
    {
        $this->metadata = $metadata;
        $this->persister = $persister;
        $this->proxyFactory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function find($index): object
    {
        $result = $this->findOneBy([$this->metadata->getIdentifier() => $index]);

        return $this->_createEntity($result);
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
        $result = $this->persister->loadAll($criteria, $orderBy, $limit, $offset);
        return array_map([$this, '_createEntity'], $result);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria): object
    {
        $result = $this->findBy($criteria, null, 1, null);

        return $this->_createEntity($result);
    }

    /**
     * Creates entities from an array
     *
     * @param array|null $data
     * @return object|null
     */
    protected function _createEntity(?array $data = null): ?object
    {
        if (!$data) {
            return null;
        }

        $proxy = $this->proxyFactory->create($this->metadata->getEntityClass(), $data);
        return $proxy->reveal();
    }

    public function getClassName(): string
    {
        return $this->metadata->getEntityClass();
    }
}
