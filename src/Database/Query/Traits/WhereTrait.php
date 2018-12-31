<?php

namespace Simplex\Database\Query\Traits;

trait WhereTrait
{
    /**
     * @var array
     */
    protected $whereTokens = [];

    /**
     * @var array
     */
    protected $whereParameters = [];

    /**
     * Add where clause
     *
     * @param string|array $criteria
     * @return self
     */
    public function where($criteria): self
    {
        if (is_array($criteria)) {
            [$tokens, $parameters] = $this->parseArray($criteria);
        } else {
            [$tokens, $parameters] = $this->parse(func_get_args());
        }

        $this->whereTokens = array_merge($this->whereTokens, $tokens);
        $this->whereParameters = array_merge($this->whereParameters, $parameters);
        return $this;
    }

    /**
     * Parse where condition from array
     *
     * @param array $criteria
     * @return array
     */
    protected function parseArray(array $criteria): array
    {
        $tokens = $parameters = [];
        foreach ($criteria as $key => $value) {
            if (is_string($key)) {
                $tokens[] = [
                    'field' => $key,
                    'op' => '='
                ];
                $parameters[] = $value;
            } elseif (is_int($key) && count($value) === 3) {
                $tokens[] = [
                    'field' => $value[0],
                    'op' => $value[1]
                ];
                $parameters[] = $value[2];
            }
        }

        return [$tokens, $parameters];
    }

    /**
     * Parse a where condition given as string
     *
     * @param array $criteria
     * @return array
     */
    protected function parse(array $criteria): array
    {
        if (2 > count($criteria)) {
            $criteria = explode(' ', $criteria[0]);
        }

        if (count($criteria) === 2) {
            $criteria[2] = $criteria[1];
            $criteria[1] = '=';
        }
        
        return [
            [
                [
                    'field' => $criteria[0],
                    'op' => $criteria[1]
                ]
            ],
            [$criteria[2]]
        ];
    }
}
