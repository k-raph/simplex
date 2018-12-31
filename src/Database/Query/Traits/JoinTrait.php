<?php

namespace Simplex\Database\Query\Traits;

trait JoinTrait
{
    protected $joinTokens = [];

    /**
     * Perform a JOIN query
     *
     * @param string $type
     * @param string $table
     * @param string $field
     * @param string $operator
     * @param string|null $targetField
     * @return self
     */
    public function join(string $type, string $table, string $field, string $operator, ?string $targetField = null): self
    {
        if (!$targetField) {
            $targetField = $operator;
            $operator = '=';
        }

        $this->joinTokens[] = [
            'type' => $type,
            'table' => $table,
            'field' => $field,
            'op' => $operator,
            'target' => $targetField
        ];

        return $this;
    }

    /**
     * Perform an INNER JOIN query
     *
     * @param string $table
     * @param string $field
     * @param string $operator
     * @param string|null $targetField
     * @return self
     */
    public function innerJoin(string $table, string $field, string $operator, ?string $targetField = null): self
    {
        return $this->join('INNER', $table, $field, $operator, $targetField);
    }

    /**
     * Perform a LEFT JOIN query
     *
     * @param string $table
     * @param string $field
     * @param string $operator
     * @param string|null $targetField
     * @return self
     */
    public function leftJoin(string $table, string $field, string $operator, ?string $targetField = null): self
    {
        return $this->join('LEFT', $table, $field, $operator, $targetField);
    }
}
