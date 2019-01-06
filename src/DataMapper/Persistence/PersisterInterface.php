<?php

namespace Simplex\DataMapper\Persistence;

interface PersisterInterface
{
    /**
     * Loads an entry matching given criteria
     *
     * @param array $criteria
     * @return mixed
     */
    public function load(array $criteria): array;

    /**
     * Loads all entries matching given filters
     *
     * @param array $criteria
     * @param string|null $orderBy
     * @param integer|null $limit
     * @param integer|null $offset
     * @return array
     */
    public function loadAll(array $criteria, ?string $orderBy = 'DESC', ?int $limit = null, ?int $offset = 0): array;
    
    /**
     * Adds an object to insert to store
     *
     * @param object $entity
     * @return void
     */
    public function addInsert(object $entity);

    /**
     * Executes insert operations
     *
     * @return void
     */
    public function performInsert();

    /**
     * Performs entity update
     *
     * @return void
     */
    public function update(object $entity, array $changes);

    /**
     * Performs entity deletion
     *
     * @param object $entity
     * @return void
     */
    public function delete(object $entity);
}
