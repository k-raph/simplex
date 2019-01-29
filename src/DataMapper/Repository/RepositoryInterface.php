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
    public function findBy(array $criteria);

    /**
     * Get a single entry matching a criteria
     *
     * @param array $criteria
     * @return object|null
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * Shortcut for updating without retrieving first
     *
     * @param $id
     * @param array $values
     * @return mixed
     */
    public function update($id, array $values);

    /**
     * Shortcut for deleting without retrieving first
     *
     * @param $id
     * @return mixed
     */
    public function remove($id);
}
