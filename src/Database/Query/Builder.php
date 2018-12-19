<?php

namespace Simplex\Database\Query;

use Simplex\Database\DatabaseInterface;
use Simplex\Database\Query\Traits\WhereTrait;
use Simplex\Database\Query\Traits\TokenTrait;

class Builder
{
    use WhereTrait, TokenTrait;

    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var string
     */
    protected $table;

    /**
     * Query Builder wrapper around specifics query classes
     *
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Query table
     *
     * @param string $table
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Insert rows into table
     *
     * @param array $values
     * @return integer
     */
    public function insert(array $values): int
    {
        $driver = $this->database->getDriver(DatabaseInterface::WRITE);
        $query = $driver->insertQuery(
            $this->database->getPrefix(), 
            $this->table
        );

        $query->values($values);
        return $query->run();
    }

    /**
     * Add where parameters to built query
     *
     * @param WhereTrait $query
     * @return void
     */
    protected function fill($query)
    {
        /* @var WhereTrait $query */
        $query->fillWhere($this->whereTokens, $this->whereParameters);
    }

    /**
     * Update rows in table
     *
     * @param array $values
     * @return integer
     */
    public function update(array $values): int
    {
        $driver = $this->database->getDriver(DatabaseInterface::WRITE);
        $query = $driver->updateQuery(
            $this->database->getPrefix(), 
            $this->table, 
            [],
            $values
        );
        $this->fill($query);

        return $query->run();
    }

    /**
     * Delete rows in table
     *
     * @return integer
     */
    public function delete(): int
    {
        $driver = $this->database->getDriver(DatabaseInterface::WRITE);
        $query = $driver->deleteQuery(
            $this->database->getPrefix(), 
            $this->table
        );
        $this->fill($query);

        return $query->run();
    }

    public function select(...$args): SelectQuery
    {
        $driver = $this->database->getDriver(DatabaseInterface::READ);
        $query = $driver->selectQuery(
            $this->database->getPrefix(),
            [$this->table],
            $args
        );

        return $query;
    }

    /**
     * Get first result of a select query
     *
     * @return void
     */
    public function first()
    {
        $query = $this->select('*');
        $this->fill($query);
        return $query->run()->fetch();
    }

    /**
     * Get all rows from a select query
     *
     * @return array
     */
    public function get(): array
    {
        $query = $this->select('*');
        $this->fill($query);
        return $query->run()->fetchAll();
    }

    /**
     * Find a result by key
     *
     * @param string|integer $key
     * @param string $field
     * @return void
     */
    public function find($key, string $field = 'id')
    {
        return $this->where($field, $key)->first();
    }

    /**
     * Find all rows matching filter
     *
     * @param array $filter
     * @return array
     */
    public function findAll(array $filter): array
    {
        return $this->where($filter)->get();
    }

}