<?php
/**
 * Created by PhpStorm.
 * User: K. Raph
 * Date: 30/10/2019
 * Time: 16:24
 */

namespace App\AskeetModule\Repository;


use Simplex\Database\Exceptions\ResourceNotFoundException;

class QuestionRepository extends AbstractRepository
{

    /**
     * @var string
     */
    protected $table = 'questions';

    /**
     * @return array
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function getAll()
    {
        return $this->findBy([]);
    }

    /**
     * @param array $criteria
     * @return array
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function findBy(array $criteria = [])
    {
        return $this->query()
            ->where($criteria)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * @return \Simplex\Database\Query\Builder
     */
    protected function query()
    {
        return $this->builder
            ->table($this->table, 'q')
            ->select(['q.id', 'title', 'slug', 'votes', 'created_at'])
            ->addSelect('u.username', 'author')
            ->leftJoin(['users', 'u'], 'u.id', 'q.author_id');
    }

    /**
     * @param int|string $id
     * @param string $key
     * @return array|mixed|null
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function find($id, string $key = 'id')
    {
        $question = $this->query()
            ->addSelect('content')
            ->where("q.$key", '=', $id)
            ->first();

        if (!$question) {
            throw new ResourceNotFoundException();
        }

        return $question;
    }
}