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

        $result = array_map([$this->mapper, 'createEntity'], $result);
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
        return $this->mapper->createEntity(
            $this->buildSelect()
                ->where('p.id', $id)
                ->first()
        );
    }
}