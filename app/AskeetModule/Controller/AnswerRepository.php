<?php
/**
 * Created by PhpStorm.
 * User: K. Raph
 * Date: 30/10/2019
 * Time: 18:56
 */

namespace App\AskeetModule\Controller;


use App\AskeetModule\Repository\AbstractRepository;

class AnswerRepository extends AbstractRepository
{

    protected $table = 'answers';

    /**
     * @param array $question
     * @return array
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function findForQuestion(array $question)
    {
        return $this->builder
            ->table($this->table, 'a')
            ->where('parent_id', '=', $question['id'])
            ->select('a.id', 'content', 'votes', 'is_best', 'updated_at')
            ->addSelect('u.username', 'author')
            ->leftJoin(['users', 'u'], 'u.id', 'a.author_id')
            ->get();
    }
}