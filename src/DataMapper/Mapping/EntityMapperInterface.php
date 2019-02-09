<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/02/2019
 * Time: 00:04
 */

namespace Simplex\DataMapper\Mapping;

use Simplex\DataMapper\QueryBuilder;

interface EntityMapperInterface
{

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return object
     */
    public function createEntity(array $input): object;

    /**
     * Extract an entity to persistable state
     *
     * @param object $entity
     * @return array
     */
    public function extract(object $entity): array;

    /**
     * Gets an entity by its primary key
     *
     * @param $id
     * @return object
     */
    public function find($id): object;

    /**
     * Retrieves all data
     *
     * @return array
     */
    public function findAll(): array;

    /**
     * Performs an entity insertion
     *
     * @param object $entity
     * @return mixed
     */
    public function insert(object $entity);

    /**
     * Performs an entity update
     *
     * @param object $entity
     * @return mixed
     */
    public function update(object $entity);

    /**
     * Performs an entity deletion
     *
     * @param object $entity
     * @return mixed
     */
    public function delete(object $entity);

    /**
     * Queue an entity for insertion
     *
     * @internal
     * @param object $entity
     * @return mixed
     */
    public function queueInsert(object $entity);

    /**
     * Performs batch insert
     *
     * @internal
     * @return mixed
     */
    public function executeInsert();

    /**
     * Gets entity table
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * @param string|null $alias
     * @return QueryBuilder
     */
    public function query(?string $alias = null): QueryBuilder;
}