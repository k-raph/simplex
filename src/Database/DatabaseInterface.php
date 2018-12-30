<?php

namespace Simplex\Database;

use PDOStatement;

interface DatabaseInterface
{
    /**
     * Makes a query against database
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function query(string $statement, array $bindings = []): PDOStatement;

    /**
     * Executes a query against database
     *
     * @param string $statement
     * @param array $bindings
     * @return bool
     */
    public function execute(string $statement, array $bindings = []): bool;

    /**
     * Wraps database operations as a transaction
     *
     * @param \Closure $transaction
     * @return bool
     */
    public function transaction(\Closure $transaction): bool;

    /**
     * Fetch a single entry from a query
     *
     * @return mixed
     */
    public function fetch();

    /**
     * Fetch a collection of entries
     *
     * @return array
     */
    public function fetchAll(): array;
}
