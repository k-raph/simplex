<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Persistence\PersisterInterface;
use Simplex\DataMapper\Proxy\Proxy;
use Simplex\DataMapper\Proxy\ProxyFactory;
use Simplex\DataMapper\Persistence\DatabasePersister;

class UnitOfWork
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var PersisterInterface[]
     */
    protected $persisters = [];

    public function __construct(EntityManager $manager, ProxyFactory $proxies)
    {
        $this->em = $manager;
        $this->proxyFactory = $proxies;
    }

    public function persist(object $entity)
    {
    }

    public function remove(object $entity)
    {
    }

    /**
     * Gets given entity class persister
     *
     * @param string $className
     * @return PersisterInterface
     */
    public function getPersister(string $className): PersisterInterface
    {
        if (isset($this->persisters[$className])) {
            $this->persisters[$className] = new DatabasePersister(
                $this->em,
                $this->em->getMetadataFor($className)
            );
        }

        return $this->persisters[$className];
    }

    /**
     * Extract values from given entity object
     *
     * @param object $entity
     * @return array
     */
    public function extract(object $entity): array
    {
        return $this->proxify($entity)->toArray();
    }

    /**
     * Retrieve a proxy wrapped around given object
     *
     * @param object $entity
     * @return Proxy|null
     */
    public function proxify(object $entity): ?Proxy
    {
        return $this->proxyFactory->wrap($entity);
    }

    
    /**
     * Creates entities from an array
     *
     * @param array $data
     * @return object|null
     */
    public function createEntity(string $className, array $data = []): ?object
    {
        $proxy = $this->proxyFactory->create($className, $data);
        return $proxy->reveal();
    }
}
