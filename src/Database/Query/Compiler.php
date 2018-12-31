<?php

namespace Simplex\Database\Query;

class Compiler
{
    /**
     * Compiles a select query
     *
     * @param string $table
     * @param array $columns
     * @param array $whereTokens
     * @param array $joinTokens
     * @param array $orderBy
     * @param integer|null $offset
     * @param integer|null $limit
     * @return string
     */
    public function compileSelect(
        string $table,
        array $columns,
        array $whereTokens = [],
        array $joinTokens = [],
        array $orderBy = [],
        ?int $offset = null,
        ?int $limit = null
    ): string {
        $sql[] = 'SELECT';
        $sql[] = implode($columns, ', ');
        $sql[] = 'FROM';
        $sql[] = $table;
        
        if ($joinTokens) {
            foreach ($joinTokens as $token) {
                $sql[] = "{$token['type']} JOIN {$token['table']} ON {$token['field']} {$token['op']} {$token['target']}";
            }
        }

        if ($whereTokens) {
            $sql[] = $this->compileWhere($whereTokens);
        }

        if ($orderBy) {
            $sql[] = 'ORDER BY';
            $sql[] = $orderBy[0].' '.$orderBy[1];
        }

        if (!is_null($offset) && !is_null($limit)) {
            $sql[] = 'LIMIT';
            $sql[] = "$offset, $limit";
        }
        
        return implode($sql, ' ');
    }

    /**
     * Compiles a delete query
     *
     * @param string $table
     * @param array $whereTokens
     * @return string
     */
    public function compileDelete(string $table, array $whereTokens = []): string
    {
        $sql[] = 'DELETE FROM';
        $sql[] = $table;
        if ($whereTokens) {
            $sql[] = $this->compileWhere($whereTokens);
        }

        return implode($sql, ' ');
    }

    /**
     * Compiles an update query
     *
     * @param string $table
     * @param array $changes
     * @param array $whereTokens
     * @return string
     */
    public function compileUpdate(string $table, array $changes, array $whereTokens = []): string
    {
        $sql[] = 'UPDATE';
        $sql[] = $table;
        $sql[] = 'SET';
        $values = array_map(function (string $field) {
            return "$field = ?";
        }, array_keys($changes));
        $sql[] = implode($values, ', ');

        if ($whereTokens) {
            $sql[] = $this->compileWhere($whereTokens);
        }

        return implode($sql, ' ');
    }

    /**
     * Compiles an insert query
     *
     * @param string $table
     * @param array $values
     * @return string
     */
    public function compileInsert(string $table, array $values): string
    {
        $sql[] = 'INSERT INTO';
        $sql[] = $table;
        $sql[] = '('. implode(array_keys($values), ', ') .')';
        $sql[] = 'VALUES(' . implode(array_fill(0, count($values), '?'), ', ') . ')';

        return implode($sql, ' ');
    }

    /**
     * Compiles a where fragment of a query
     *
     * @param array $tokens
     * @return string
     */
    protected function compileWhere(array $tokens): string
    {
        $clauses = array_map(function ($token) {
            return $token['field']. ' ' . $token['op'] . ' ?';
        }, $tokens);

        return 'WHERE '. implode($clauses, ' AND ');
    }
}
