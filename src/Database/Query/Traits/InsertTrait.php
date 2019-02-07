<?php

namespace Simplex\Database\Query\Traits;

use Finesse\QueryScribe\StatementInterface;
use Simplex\Database\DatabaseInterface;
use Simplex\Database\Exceptions\DatabaseException;
use Simplex\Database\Exceptions\IncorrectQueryException;
use Simplex\Database\Exceptions\InvalidArgumentException;
use Simplex\Database\Query\Builder;

/**
 * Contains methods for performing insert queries with Query.
 *
 * @mixin Builder
 *
 * @author Surgie
 */
trait InsertTrait
{
    /**
     * @var DatabaseInterface Database on which the query should be performed
     */
    protected $connection;

    /**
     * Inserts a row to a table and returns the inserted row identifier. Doesn't modify itself.
     *
     * @param mixed[]|\Closure[]|Query[]|StatementInterface[] $row Row. Associative array where indexes are column
     *     names and values are cell values. Rows indexes must be strings.
     * @param string|null $sequence Name of the sequence object from which the ID should be returned
     * @return int|string
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     */
    public function insertGetId(array $row, string $sequence = null)
    {
        try {
            $query = (clone $this)->addInsert([$row]);//->apply($this->connection->getTablePrefixer());
            $statements = $this->connection->getDriver()->getGrammar()->compileInsert($query);

            $id = null;
            foreach ($statements as $statement) {
                $this->connection->execute($statement->getSQL(), $statement->getBindings(), $sequence);
                $id = $this->connection->lastInsertId();
            }

            return $id;
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Inserts rows to a table from a select query. Doesn't modify itself.
     *
     * @param string[]|\Closure|Query|StatementInterface $columns The list of the columns to which the selected values
     *     should be inserted. You may omit this argument and pass the $selectQuery argument instead.
     * @param \Closure|self|StatementInterface|null $selectQuery
     * @return int Number of inserted rows
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     */
    public function insertFromSelect($columns, $selectQuery = null): int
    {
        return (clone $this)->addInsertFromSelect($columns, $selectQuery)->insert([]);
    }

    /**
     * Inserts rows to a table. Doesn't modify itself.
     *
     * @param mixed[][]|\Closure[][]|Query[][]|StatementInterface[][] $rows An array of rows. Each row is an associative
     *     array where indexes are column names and values are cell values. Rows indexes must be strings.
     * @return int Number of inserted rows
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     */
    public function insert(array $rows): int
    {
        try {
            $query = (clone $this)->addInsert($rows); // ->apply($this->database->getTablePrefixer());
            $statements = $this->connection->getDriver()->getGrammar()->compileInsert($query);

            $count = 0;
            foreach ($statements as $statement) {
                $count += $this->connection->execute($statement->getSQL(), $statement->getBindings());
            }

            return $count;
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }
}
