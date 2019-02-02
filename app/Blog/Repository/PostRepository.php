<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29/01/2019
 * Time: 06:28
 */

namespace App\Blog\Repository;


use Simplex\Database\Query\Builder;
use Simplex\DataMapper\Repository\Repository;

class PostRepository extends Repository
{

    /**
     * @return array
     * @throws \Throwable
     */
    public function findAll(): array
    {
        $result = $this->buildSelect()->get();

        return $result;
    }

    /**
     * Select query builder helper
     *
     * @return Builder
     */
    private function buildSelect(): Builder
    {
        return $this->query('p')
            ->addSelect(['p.id', 'title', 'slug', 'content'])
            ->addSelect('u.username', 'author_id')
            ->innerJoin(['users', 'u'], 'p.author_id', 'u.id');
    }

    /**
     * @param mixed $id
     * @return object|null
     */
    public function find($id): ?object
    {
        return $this->buildSelect()
            ->where('p.id', $id)
            ->first();
    }

    /**
     * Gets result for given page
     *
     * @return Builder
     */
    public function queryForIndex(): Builder
    {
        return $this->buildSelect();
    }

    /**
     * Checks wether a post exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id)
    {
        return (bool)$this->query('p')
            ->where('p.id', $id)
            ->count();
    }
}