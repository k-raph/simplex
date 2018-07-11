<?php

namespace Simplex\Database;

class QueryBuilder
{

    private $type;

    private $select;

    private $table;

    private $where = [];

    private $params = [];

    private $join;

    private $on;

    private $sql;

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
        $this->type = 'read';
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
    public function table($table, $alias = null)
    {
        if (is_null($alias)) {
            $this->table[] = $table;
        } else {
            $this->table[$alias] = $table;
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
        if (!$this->sql) {

            $parts = [];
            
            switch($this->type) {
                case 'read': {
                    $parts = $this->buildSelect();
                    break;
                }
                case 'update': {
                    $parts = $this->buildUpdate();
                    break;
                }
                case 'create': {
                    $parts = $this->buildInsert();
                    break;
                }

                case 'delete': {
                    $parts = $this->buildDelete();
                    break;
                }
                default: throw new \Exception('Bingo');
            }
        
            if (!empty($this->where)) {
                $parts[] = 'WHERE';
                $parts[] = implode(' AND ', $this->where);
            }

            $this->sql = implode(' ', $parts);
        }

        return $this->sql;
    }
    
    /**
     * Build the from part of the query
     *
     * @return string
     */
    private function buildTable()
    {
        $parts = [];
        foreach($this->table as $alias => $table) {
            if(is_string($alias))
                $parts[] = "$table AS $alias";
            else
                $parts[] = $table;
        }

        return implode(',', $parts);
    }

    protected function buildSelect()
    {
        $parts = [];
        $parts[] = 'SELECT';
        $parts[] = implode(',', $this->select);

        $parts[] = 'FROM';
        $parts[] = $this->buildTable();
    
        if (!empty($this->join)) {
            $parts[] = 'INNER JOIN';
            $parts[] = $this->join;
        }

        if (!empty($this->on)) {
            $parts[] = 'ON';
            $parts[] = $this->on;
        }

        return $parts;
    }

    /**
     * Execute the request
     * 
     * @return \PDOStatement
     */
    public function execute(): \PDOStatement
    {
        if (empty($this->where) && empty($this->params)) {
            return $this->pdo->query($this->getSql());
        }

        $stmt = $this->pdo->prepare($this->getSql());
        $stmt->execute($this->params);
        $this->init();
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

    public function update(array $data): self
    {
        $this->type = 'update';
        $this->params = array_merge($data, $this->params);

        return $this;
    }

    public function insert(array $data): self
    {
        $this->type = 'create';
        $this->params = array_merge($data, $this->params);

        return $this;
    }

    public function delete(): self
    {
        $this->type = 'delete';

        return $this;
    }

    protected function buildDelete()
    {
        $parts = [];
        $parts[] = 'DELETE FROM';
        $parts[] = $this->buildTable();

        return $parts;
    }

    protected function buildInsert()
    {
        $params = array_keys($this->params);
        $parts = [];
        $parts[] = 'INSERT INTO';
        $parts[] = $this->buildTable();
        $parts[] = '('.implode(', ', $params).')';
        $parts[] = 'VALUES('. implode(', ', array_map(function($value) { return ":$value";}, $params)).')';

        return $parts;
    }

    protected function buildUpdate()
    {
        $parts = [];
        $parts[] =  'UPDATE';
        $parts[] = $this->buildTable();
        $parts[] = 'SET';

        $vars = [];
        foreach($this->params as $key => $value) {
            $vars[] = "$key = :$key";
        }

        $parts[] = implode(", ", $vars);
        return $parts;
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

    private function init()
    {
        $this->type = $this->table = $this->select = $this->join = $this->on = $this->sql = null;
        $this->where = $this->params = [];
    }
}
