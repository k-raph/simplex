<?php

namespace Simplex\DataMapper\Repository;

interface RepositoryInterface
{
    /**
     * Gets an entry by its primary primary key
     *
     * @param mixed $index
     * @return object
     */
    public function find($index): object;

    /**
     * Retrieve all of the entries
     *
     * @return object[]
     */
    public function findAll(): array;

    /**
     * Get an array of results after a filter
     *
     * @param array $criteria
     * @return object[]
     */
    public function findBy(array $criteria, ?string $orderBy = 'DESC', ?int $limit = null, ?int $offset): array;

    /**
     * Get a single entry matching a criteria
     *
     * @param array $criteria
     * @return object
     */
    public function findOneBy(array $criteria): object;

    /**
     * Get managed entity class name
     *
     * @return string
     */
    public function getClassName(): string;
}
