<?php

namespace Simplex\Database\Query;

use Finesse\QueryScribe\PostProcessors\ExplicitTables;
use Finesse\QueryScribe\Query;
use Finesse\QueryScribe\StatementInterface;
use Simplex\Database\DatabaseInterface;
use Simplex\Database\Exceptions\DatabaseException;
use Simplex\Database\Exceptions\IncorrectQueryException;
use Simplex\Database\Exceptions\InvalidArgumentException;
use Simplex\Database\Exceptions\InvalidReturnValueException;
use Simplex\Database\Helpers;
use Simplex\Database\Query\Traits\InsertTrait;
use Simplex\Database\Query\Traits\RawHelpersTrait;
use Simplex\Database\Query\Traits\SelectTrait;

/**
 * Query builder. Builds SQL queries and performs them on a database.
 *
 * All the methods throw Simplex\Database\Exceptions\ExceptionInterface.
 *
 * {@inheritDoc}
 *
 * @author Surgie
 */
class Builder extends Query
{
    use SelectTrait, InsertTrait, RawHelpersTrait;

    /**
     * @var DatabaseInterface Database on which the query should be performed
     */
    protected $connection;

    /**
     * @param DatabaseInterface $database Database on which the query should be performed
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->connection = $database;
        $this->grammar = $database->getDriver()->getGrammar();
    }

    /**
     * Updates the query target rows. Doesn't modify itself.
     *
     * @param mixed[]|\Closure[]|self[]|StatementInterface[] $values Fields to update. The indexes are the columns
     *     names, the values are the values.
     * @return int The number of updated rows
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function update($values): int
    {
        try {
            $query = (clone $this)->addUpdate($values); //->apply($this->connection->getTablePrefixer());
            $compiled = $this->connection->getDriver()->getGrammar()->compileUpdate($query);
            return $this->connection->execute($compiled->getSQL(), $compiled->getBindings());
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function handleException(\Throwable $exception)
    {
        try {
            return parent::handleException($exception);
        } catch (\Throwable $exception) {
            throw Helpers::wrapException($exception);
        }
    }

    /**
     * Deletes the query target rows. Doesn't modify itself.
     *
     * @return int The number of deleted rows
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function delete(): int
    {
        try {
            $query = (clone $this)->setDelete(); //->apply($this->connection->getTablePrefixer());
            $compiled = $this->connection->getDriver()->getGrammar()->compileDelete($query);
            return $this->connection->execute($compiled->getSQL(), $compiled->getBindings());
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Makes the query have explicit tables in the column names.
     *
     * Warning! In contrast to the other methods, it doesn't modify the query object, it returns a new object.
     *
     * @return static
     */
    public function addTablesToColumnNames(): self
    {
        return $this->apply(new ExplicitTables);
    }

    /**
     * {@inheritDoc}
     */
    protected function constructEmptyCopy(): Query
    {
        return new static($this->connection);
    }

    /**
     * @return Builder
     */
    public function newQuery(): self
    {
        return new self($this->connection);
    }

    /**
     * @param string[]|array[] ...$arguments
     * @return Query
     */
    public function where(...$arguments): Query
    {
        $count = count($arguments);
        if (2 === $count || 3 === $count) {
            return parent::where(...$arguments);
        }

        $criterion = [];
        foreach ($arguments as $argument) {
            foreach ($argument as $key => $value) {
                if (is_string($key)) {
                    $criterion[] = [$key, $value];
                } elseif (is_array($value)) {
                    $criterion[] = $value;
                }
            }
        }

        return parent::where($criterion);
    }

    /**
     * Gets compiled sql
     *
     * @return string
     */
    public function getSql(): string
    {
        return $this->grammar
            ->compile($this)
            ->getSQL();
    }
}
