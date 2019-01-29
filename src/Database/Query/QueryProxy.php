<?php

namespace Simplex\Database;

use Finesse\QueryScribe\Query;
use Finesse\QueryScribe\QueryProxy as BaseQueryProxy;
use Simplex\Database\Query\Traits\SelectTrait;

/**
 * Helps to extend a query object dynamically.
 *
 * All the methods throw Simplex\Database\Exceptions\ExceptionInterface.
 *
 * {@inheritDoc}
 *
 * @mixin Query
 *
 * @author Sugrie
 */
class QueryProxy extends BaseQueryProxy
{
    /**
     * {@inheritDoc}
     * @param Query $baseQuery
     */
    public function __construct(Query $baseQuery)
    {
        parent::__construct($baseQuery);
    }

    /**
     * {@inheritDoc}
     * @return mixed[]
     * @see SelectTrait::get
     * @throws \Throwable
     */
    public function get(): array
    {
        try {
            $rows = $this->baseQuery->get();
            return array_map([$this, 'processFetchedRow'], $rows);
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
     * {@inheritDoc}
     * @return mixed
     * @see SelectTrait::first
     * @throws \Throwable
     */
    public function first()
    {
        try {
            $row = $this->baseQuery->first();

            if ($row === null) {
                return $row;
            } else {
                return $this->processFetchedRow($row);
            }
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Processes a row fetched from the database before returning it.
     *
     * @param array $row
     * @return mixed
     */
    protected function processFetchedRow(array $row)
    {
        return $row;
    }

    /**
     * {@inheritDoc}
     * @see SelectTrait::chunk
     */
    public function chunk(int $size, callable $callback)
    {
        try {
            return $this->baseQuery->chunk($size, function (array $rows) use ($callback) {
                $callback(array_map([$this, 'processFetchedRow'], $rows));
            });
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * {@inheritDoc}
     * Changes the current object instead of returning a new object.
     *
     * @return $this
     */
    public function addTablesToColumnNames(): self
    {
        $this->baseQuery = $this->baseQuery->addTablesToColumnNames();
        return $this;
    }
}
