<?php

namespace Simplex\Database\Query;

use Simplex\Database\DatabaseInterface;
use Simplex\Database\Query\Traits\WhereTrait;
use Simplex\Database\Query\Compiler\SelectCompiler;
use Simplex\Database\Query\Traits\JoinTrait;
use PDOStatement;
use Simplex\Database\Exception\ResourceNotFoundException;

class Builder
{
    use WhereTrait, JoinTrait;

    /**
     * @var string
     */
    protected $table;

    /**
     * Type of query
     *
     * @var int
     */
    protected $type;

    /**
     * Filtering data
     *
     * @var array
     */
    protected $orderBy = [];

    /**
     * Query limit value
     *
     * @var int
     */
    protected $limit;

    /**
     * Query offset value
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * Fields to select
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Sql string statement
     *
     * @var string
     */
    protected $sql = '';

    /**
     * Values to insert into database
     *
     * @var array
     */
    protected $values = [];

    /**
     * Bound parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Database connection instance
     *
     * @var DatabaseInterface
     */
    protected $db;
    
    protected const TYPE_INSERT = 0;
    protected const TYPE_SELECT = 1;
    protected const TYPE_UPDATE = 2;
    protected const TYPE_DELETE = 3;
    
    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function newQuery(): Builder
    {
        return new self($this->db);
    }

    /**
     * Set table against which to make the query
     *
     * @param string $table
     * @return self
     */
    public function table(string $table, ?string $alias = null): self
    {
        $this->table = is_null($alias) ? $table : "$table AS $alias";
        $this->type = self::TYPE_SELECT;
        return $this;
    }

    /**
     * Add an order by constraint
     *
     * @param string $field
     * @param string $filter
     * @return self
     */
    public function orderBy(string $field, string $filter = 'DESC'): self
    {
        $this->orderBy = [$field, $filter];
        return $this;
    }

    /**
     * Adds a limit constraint
     *
     * @param integer $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Adds an offset constraint
     *
     * @param integer $offset
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Choose fields to select
     *
     * @param string ...$fields
     * @return self
     */
    public function select(string ...$fields): self
    {
        $this->type = self::TYPE_SELECT;
        $this->fields = array_merge($this->fields, empty($fields) ? ['*'] : $fields);
        return $this;
    }

    /**
     * Makes an insert query to database
     *
     * @param array $values
     * @return self
     */
    public function insert(array $values): self
    {
        $this->type = self::TYPE_INSERT;
        $this->values = $values;
        return $this;
    }

    /**
     * Makes an update query against database
     *
     * @param array $values
     * @return self
     */
    public function update(array $values): self
    {
        $this->type = self::TYPE_UPDATE;
        $this->values = $values;
        return $this;
    }

    /**
     * Makes a delete query against database
     *
     * @return self
     */
    public function delete(): self
    {
        $this->type = self::TYPE_DELETE;
        return $this;
    }

    /**
     * Get generated sql statement string
     *
     * @return string
     */
    public function getSql(): string
    {
        if ($this->sql) {
            return $this->sql;
        }

        $compiler = new Compiler();
        switch ($this->type) {
            case self::TYPE_SELECT:
                $this->sql = $compiler->compileSelect(
                    $this->table,
                    empty($this->fields) ? ['*'] : $this->fields,
                    $this->whereTokens,
                    $this->joinTokens,
                    $this->orderBy,
                    $this->offset,
                    $this->limit
                );
                break;
            case self::TYPE_UPDATE:
                $this->sql = $compiler->compileUpdate(
                    $this->table,
                    $this->values,
                    $this->whereTokens
                );
                break;
            case self::TYPE_INSERT:
                $this->sql = $compiler->compileInsert(
                    $this->table,
                    $this->values
                );
                break;
            case self::TYPE_DELETE:
                $this->sql = $compiler->compileDelete(
                    $this->table,
                    $this->whereTokens,
                    $this->whereParameters
                );
                break;
            default:
                throw new \Exception('This point should never be reached');
                break;
        }

        return $this->sql;
    }

    /**
     * Get bound parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return array_merge(array_values($this->values), $this->whereParameters);
    }

    /**
     * Run builded query
     *
     * @return PDOStatement|bool
     */
    public function run()
    {
        $result = $this->type === self::TYPE_SELECT
            ? $this->db->query($this->getSql(), $this->getParameters())
            : $this->db->execute($this->getSql(), $this->getParameters());
        $this->reset();

        return $result;
    }

    /**
     * Reset all the properties
     *
     * @return void
     */
    protected function reset()
    {
        // $this->table = null;
        $this->type = null;
        $this->sql = '';
        $this->fields = [];
        $this->values = [];
        $this->whereTokens = [];
        $this->whereParameters = [];
        $this->joinTokens = [];
    }

    /**
     * ---------------------------------------------------------
     *
     * Advanced query helper
     *
     * ---------------------------------------------------------
     */

    /**
     * Makes raw query to database
     *
     * @param string $query
     * @return self
     */
    public function raw(string $query): self
    {
        $this->sql = $query;
        return $this;
    }

    /**
     * Bind parameters to query
     *
     * @param array $parameters
     * @return self
     */
    public function bind(array $parameters): self
    {
        $this->values = $parameters;
        return $this;
    }

    /**
     * Use the query builder to make a subquery
     *
     * @param Builder $query
     * @param string|null $alias
     * @return string
     */
    public function subQuery(Builder $query, ?string $alias = null): string
    {
        $sql = '('.$query->getSql().')';

        return $alias
            ? $sql . " AS $alias"
            : $sql;
    }

    /**
     * ----------------------------------------------------------
     *
     * Database operations decorators
     *
     * ----------------------------------------------------------
     */

    /**
     * Retrieve all results for made query
     *
     * @return array
     */
    public function get(): array
    {
        return $this->run()->fetchAll();
    }

    /**
     * Retrieve only the first result that match the query
     *
     * @return mixed
     */
    public function first()
    {
        return $this->limit(1)->run()->fetch();
    }

    /**
     * Finds the first result or throw an exception
     *
     * @return mixed
     */
    public function firstOrFail()
    {
        $result = $this->first();

        if (!$result) {
            throw new ResourceNotFoundException();
        }

        return $result;
    }

    /**
     * Find a specific entry
     *
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function find($value, string $key = 'id')
    {
        return $this->where($value, $key)->first();
    }

    /**
     * Find an entry or throw an exception
     *
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function findOrFail($value, string $key = 'id')
    {
        return $this->where($value, $key)->firstOrFail();
    }

    /**
     * Find all entries matching a criteria
     *
     * @param [type] $value
     * @param string $key
     * @return void
     */
    public function findAll($value, string $key = 'id')
    {
        return $this->where($value, $key)->get();
    }

    public function transaction(\Closure $transaction)
    {
        return $this->db->transaction($transaction, $this);
    }
}
