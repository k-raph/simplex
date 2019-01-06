<?php

namespace Simplex\DataMapper\Repository;

interface RepositoryInterface
{
    /**
     * Gets an entry by its primary primary key
     *
     * @param mixed $id
     * @return object|null
     */
    public function find($id): ?object;

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
     * @param string|null $orderBy
     * @param integer|null $limit
     * @param integer $offset
     * @return object[]
     */
    public function findBy(array $criteria, ?string $orderBy = 'DESC', ?int $limit = null, int $offset = 0): array;

    /**
     * Get a single entry matching a criteria
     *
     * @param array $criteria
     * @return object|null
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * Get managed entity class name
     *
     * @return string
     */
    public function getClassName(): string;
}
