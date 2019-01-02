<?php

namespace Simplex\DataMapper\Repository;

class Factory
{

    /**
     * Registered repositories
     *
     * @var RepositoryInterface[]
     */
    protected $repositories = [];

    /**
     * Add a class metadata instance
     *
     * @param string $className
     * @param RepositoryInterface $metadata
     * @return void
     */
    public function setClassRepository(string $className, RepositoryInterface $repository)
    {
        $this->repositories[$className] = $repository;
    }

    /**
     * Get metadata instance associed to a classname
     *
     * @param string $className
     * @return EntityMetadata|null
     */
    public function getClassRepository(string $className): ?RepositoryInterface
    {
        if (!$this->hasRepositoryFor($className)) {
            throw new \Exception(sprintf('Repository for entity class %s not found', $className));
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
}
