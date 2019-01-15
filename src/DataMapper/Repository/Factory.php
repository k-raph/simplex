<?php

namespace Simplex\DataMapper\Repository;

use Simplex\DataMapper\EntityManager;

class Factory
{
    /**
     * Registered repositories
     *
     * @var RepositoryInterface[]
     */
    protected $repositories = [];

    /**
     * Get metadata instance associed to a classname
     *
     * @param string $className
     * @return EntityMetadata|null
     */
    public function getRepository(EntityManager $manager, string $className): RepositoryInterface
    {
        if (!$this->hasRepositoryFor($className)) {
            $this->repositories[$className] = $this->createRepository($manager, $className);
        }

        return $this->repositories[$className];
    }

    /**
     * Check entity class metadata existence
     *
     * @param string $className
     * @return boolean
     */
    public function hasRepositoryFor(string $className): bool
    {
        return isset($this->repositories[$className]);
    }

    /**
     * Create an instance of repository
     *
     * @param EntityManager $manager
     * @param string $className
     * @return RepositoryInterface
     */
    protected function createRepository(EntityManager $manager, string $className): RepositoryInterface
    {
        $mapper = $manager->getMapperFor($className);
        $repository = $mapper->getMetadata()->getRepositoryClass();

        return new $repository($manager, $mapper);
    }
}
