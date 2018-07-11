<?php

namespace App\Blog\Table;

use Simplex\Database\QueryBuilder;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


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
            ->table('posts', 'p')
            ->select('p.title', 'p.content', 'u.username AS author')
            ->join('users', 'u')
            ->on('p.author_id = u.id')
            ->getAll();
    }

    public function find(int $id)
    {
        $result = $this->query
            ->table('posts', 'p')
            ->select('p.id', 'p.title', 'p.content', 'u.username AS author')
            ->join('users', 'u')
            ->on('p.author_id = u.id')
            ->where('p.id = :id')
            ->params(compact('id'))
            ->get() ?: null;
        
        if(null === $result) throw new ResourceNotFoundException();
        return $result;
    }

    public function update(int $id, array $data)
    {
        $data = array_filter($data, function($key){
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        return $this->query
            ->table('posts')
            ->where('id = :id')
            ->update($data)
            ->params(compact('id'))
            ->execute();
    }

    public function insert(array $data)
    {
        $data = array_filter($data, function($key){
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        return $this->query
            ->table('posts')
            ->insert($data)
            ->execute();
    }

    public function delete(int $id)
    {
        return $this->query
            ->table('posts')
            ->where('id = :id')
            ->delete()
            ->params(compact('id'))
            ->execute();
    }
}