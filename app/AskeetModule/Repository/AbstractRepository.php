<?php
/**
 * Created by PhpStorm.
 * User: K. Raph
 * Date: 30/10/2019
 * Time: 16:25
 */

namespace App\AskeetModule\Repository;

use Simplex\Database\DatabaseInterface;
use Simplex\Database\Query\Builder;

abstract class AbstractRepository
{

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var DatabaseInterface
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * AbstractRepository constructor.
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->db = $database;
        $this->builder = $database->getQueryBuilder()->table($this->table);
    }

    /**
     * @param int|string $id
     * @param string $key
     * @return mixed|null
     */
    public function find($id, string $key = 'id')
    {
        return $this->builder
                ->where($key, '=', $id)
                ->first() ?? null;
    }

    /**
     * @param array $criteria
     * @return array
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function findBy(array $criteria = [])
    {
        return $this->builder
                ->where($criteria)
                ->get() ?? [];
    }

    /**
     * @param array $value
     * @return integer|null
     */
    public function insert(array $value)
    {
        return $this->builder
            ->insert($value);
    }

    /**
     * @param int|string $id
     * @param array $values
     * @param string $key
     * @return int
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function update($id, array $values, string $key = 'id')
    {
        return $this->builder
            ->where($key, '=', $id)
            ->update($values);
    }

    /**
     * @param int|string $id
     * @param string $key
     * @return int
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function delete($id, string $key = 'id')
    {
        return $this->builder
            ->where($key, '=', $id)
            ->delete();
    }
}