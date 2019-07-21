<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20/07/2019
 * Time: 22:13
 */

namespace App\JobeetModule\Admin\Repository;

use App\JobeetModule\Repository\JobRepository as BaseJobRepository;

class JobRepository extends BaseJobRepository
{

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->query('j')
            ->addSelect(['j.id', 'company', 'position', 'type'])
            ->addSelect('c.name', 'category')
            ->addSelect('is_public', 'public')
            ->innerJoin(['categories', 'c'], 'j.category_id', 'c.id')
            ->get();
    }

    /**
     * @param int $id
     * @return int
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function delete(int $id)
    {
        return $this->query()
            ->where(['id' => $id])
            ->delete();
    }
}
