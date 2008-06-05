<?php

namespace App\Blog\Table;

use Simplex\Database\QueryBuilder;


class PostTable
{

    /**
     * Query builder
     *
     * @var QueryBuilder
     */
    private $query;

    /**
     * Constructor
     *
     * @param QueryBuilder $query
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query; //->from('posts');
    }

    public function getAll()
    {
        return $this->query
            ->select('p.title', 'p.content', 'u.username AS author')
            ->from('posts', 'p')
            ->join('users', 'u')
            ->on('p.author_id = u.id')
            ->getAll();
    }

    public function find(int $id)
    {
        return $this->query
            ->select('p.title', 'p.content', 'u.username AS author')
            ->from('posts', 'p')
            ->join('users', 'u')
            ->on('p.author_id = u.id')
            ->where('p.id = :id')
            ->params(compact('id'))
            ->get() ?: null;
    }
}