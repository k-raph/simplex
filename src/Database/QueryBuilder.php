<?php

namespace Simplex\Database;

class QueryBuilder
{

    private $select;

    private $from;

    private $where = [];

    private $params = [];

    private $join;

    private $on;

    /**
     * PDO instance
     *
     * @var \PDO
     */
    private $pdo;

    public function __construct(Connection $db)
    {
        $this->pdo = $db->getPdo();
    }

    /**
     * Add fields to select
     *
     * @param string ...$fields
     * @return self
     */
    public function select(...$fields)
    {
        $this->select = $fields ?: ['*'];

        return $this;
    }

    /**
     * Bind params
     *
     * @param array $params
     * @return self
     */
    public function params(array $params)
    {
        $this->params = array_merge($this->params, $params);
        
        return $this;
    }

    /**
     * Add table to select from
     *
     * @param string $table
     * @param string $alias
     * @return self
     */
    public function from($table, $alias = null)
    {
        if (is_null($alias)) {
            $this->from[] = $table;
        } else {
            $this->from[$alias] = $table;
        }

        return $this;
    }

    /**
     * Add where part
     *
     * @param string ...$condition
     * @return self
     */
    public function where(...$condition)
    {
        $this->where = array_merge($this->where, $condition);

        return $this;
    }

    /**
     * Get raw sql query
     *
     * @return string
     */
    public function getSql(): string
    {
        $parts = [];
        $parts[] = 'SELECT';
        $parts[] = implode(',', $this->select);

        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
    
        if (!empty($this->join)) {
            $parts[] = 'INNER JOIN';
            $parts[] = $this->join;
        }

        if (!empty($this->on)) {
            $parts[] = 'ON';
            $parts[] = $this->on;
        }
    
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = implode(' AND ', $this->where);
        }
           
        return implode(' ', $parts);
    }

    /**
     * Build the from part of the query
     *
     * @return string
     */
    private function buildFrom()
    {
        $parts = [];
        foreach($this->from as $alias => $table) {
            if(is_string($alias))
                $parts[] = "$table AS $alias";
            else
                $parts[] = $table;
        }

        return implode(',', $parts);
    }

    /**
     * Execute the request
     *
     * @return \PDOStatement
     */
    public function execute()
    {
        if (empty($this->where) && empty($this->params)) {
            return $this->pdo->query($this->getSql());
        }

        $stmt = $this->pdo->prepare($this->getSql());
        $stmt->execute($this->params);
        return $stmt;
    }

    public function join(string $on, string $alias)
    {
        $alias = $alias ?: $on;
        $this->join = "$on $alias";

        return $this;
    }

    public function on(string $join)
    {
        $this->on = $join;
        return $this;
    }

    /**
     * Fetch all results
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->execute()->fetchAll();
    }

    /**
     * Get single result
     *
     * @return mixed
     */
    public function get()
    {
        return $this->execute()->fetch();
    }
}
