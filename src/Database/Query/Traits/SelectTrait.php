<?php

namespace Simplex\Database\Query\Traits;

use Finesse\QueryScribe\StatementInterface;
use Simplex\Database\Database;
use Simplex\Database\Exceptions\DatabaseException;
use Simplex\Database\Exceptions\IncorrectQueryException;
use Simplex\Database\Exceptions\InvalidArgumentException;
use Simplex\Database\Exceptions\InvalidReturnValueException;
use Simplex\Database\Query\Builder;

/**
 * Contains methods for performing select queries with Query.
 *
 * @mixin Builder
 *
 * @author Surgie
 */
trait SelectTrait
{
    /**
     * @var Database Database on which the query should be performed
     */
    protected $connection;

    /**
     * Performs a select query and returns the first selected row. Doesn't modify itself.
     *
     * @return array|null An array indexed by columns. Null if nothing is found.
     * @throws \Throwable
     */
    public function first()
    {
        try {
            $query = (clone $this)->limit(1); //->apply($this->database->getTablePrefixer());
            $compiled = $this->connection->getDriver()->getGrammar()->compileSelect($query);
            return $this->connection
                ->query($compiled->getSQL(), $compiled->getBindings())
                ->fetch();
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Gets the count of the target rows. Doesn't modify itself.
     *
     * @param string|\Closure|self|StatementInterface $column Column to count
     * @return int
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function count($column = '*'): int
    {
        try {
            return $this->getAggregate(function (Query $query) use ($column) {
                $query->addCount($column);
            });
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Gets an aggregate value (count, sum, etc.) from this query
     *
     * @param callable $prepareAggregate Takes a query object and must add an aggregate to the SELECT part of the query
     * @return mixed
     */
    protected function getAggregate(callable $prepareAggregate)
    {
        $query = clone $this;
        $query->select = [];
        $query = $query
            ->offset(null)
            ->limit(null)
            ->apply($prepareAggregate);
        //->apply($this->database->getTablePrefixer());

        $compiled = $this->connection->getDriver()->getGrammar()->compileSelect($query);

        return current(
            $this->connection
                ->query($compiled->getSQL(), $compiled->getBindings())
                ->fetch()
        );
    }

    /**
     * Gets the average value of the target rows. Doesn't modify itself.
     *
     * @param string|\Closure|self|StatementInterface $column Column to get average
     * @return float|null Null is returned when no target row has a value
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function avg($column)
    {
        try {
            return $this->getAggregate(function (Query $query) use ($column) {
                $query->addAvg($column);
            });
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Gets the sum of the target rows. Doesn't modify itself.
     *
     * @param string|\Closure|self|StatementInterface $column Column to get sum
     * @return float|null Null is returned when no target row has a value
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function sum($column)
    {
        try {
            return $this->getAggregate(function (Query $query) use ($column) {
                $query->addSum($column);
            });
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Gets the minimum value of the target rows. Doesn't modify itself.
     *
     * @param string|\Closure|self|StatementInterface $column Column to get minimum
     * @return float|null Null is returned when no target row has a value
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function min($column)
    {
        try {
            return $this->getAggregate(function (Query $query) use ($column) {
                $query->addMin($column);
            });
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Gets the maximum value of the target rows. Doesn't modify itself.
     *
     * @param string|\Closure|self|StatementInterface $column Column to get maximum
     * @return float|null Null is returned when no target row has a value
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws InvalidReturnValueException
     * @throws \Throwable
     */
    public function max($column)
    {
        try {
            return $this->getAggregate(function (Query $query) use ($column) {
                $query->addMax($column);
            });
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Walks large amount of rows calling a callback on small portions of rows. Doesn't modify itself.
     *
     * @param int $size Number of rows per callback call
     * @param callable $callback The callback. Receives an array of rows as the first argument.
     * @throws DatabaseException
     * @throws IncorrectQueryException
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    public function chunk(int $size, callable $callback)
    {
        if ($size <= 0) {
            $this->handleException(new InvalidArgumentException('Chunk size must be greater than zero'));
        }

        // A copy is made not to mutate this query
        $query = clone $this;

        for ($offset = 0; ; $offset += $size) {
            $rows = $query->offset($offset)->limit($size)->get();
            if (empty($rows)) {
                break;
            }

            $callback($rows);

            if (count($rows) < $size) {
                break;
            }
        }
    }

    /**
     * Performs a select query and returns the selected rows. Doesn't modify itself.
     *
     * @return array[] Array of the result rows. Result row is an array indexed by columns.
     * @throws \Throwable
     */
    public function get(): array
    {
        try {
            $compiled = $this->connection->getDriver()->getGrammar()->compileSelect($this);
            return $this->connection
                ->query($compiled->getSQL(), $compiled->getBindings())
                ->fetchAll();
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }
}
