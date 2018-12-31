<?php

namespace App\Blog\Table;

use Simplex\Database\Query\Builder;

class PostTable
{

    /**
     * Fillable fields
     *
     * @var array
     */
    private $fillable = [
        'title',
        'content',
        'slug',
        'author_id',
    ];

    /**
     * Query builder
     *
     * @var Builder
     */
    private $query;

    /**
     * Constructor
     *
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query; //->from('posts');
    }

    public function getAll()
    {
        return $this->query
            ->table('posts', 'p')
            ->select('p.title', 'p.content', 'u.username AS author')
            ->innerJoin('users u', 'p.author_id', '=', 'u.id')
            ->get();
    }

    public function find(int $id)
    {
        return $this->query
            ->table('posts', 'p')
            ->select('p.id', 'p.title', 'p.content', 'u.username AS author')
            ->innerJoin('users u', 'p.author_id', '=', 'u.id')
            ->where('p.id', $id)
            ->firstOrFail();
    }

    public function update(int $id, array $data)
    {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        return $this->query
            ->table('posts')
            ->where('id', $id)
            ->update($data)
            ->run();
    }

    public function insert(array $data)
    {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        return $this->query
            ->table('posts')
            ->insert($data)
            ->run();
    }

    public function delete(int $id)
    {
        return $this->query
            ->table('posts')
            ->delete()
            ->where('id', $id)
            ->execute();
    }
}
