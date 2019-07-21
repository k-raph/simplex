<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21/07/2019
 * Time: 12:25
 */

namespace App\JobeetModule\Admin\Repository;

use App\JobeetModule\Repository\CategoryRepository as BaseRepository;

class CategoryRepository extends BaseRepository
{

    /**
     * @return array
     * @throws \Simplex\Database\Query\Exception\QueryExecutionFailException
     */
    public function findAll(): array
    {
        $query = $this->query()
            ->nativeQuery();

        $c_id = $query->raw('`c`.`id`');
        $jobs = $query->newQuery()
            ->table('jobs', 'j')
            ->where(['j.category_id' => $c_id])
            ->addSelect($query->raw('COUNT(`j`.`id`)'));

        $affiliates = $query->newQuery()
            ->table('affiliate_category', 'p')
            ->where(['p.category_id' => $c_id])
            ->addSelect($query->raw('COUNT(`p`.`id`)'));

        $query = $query->table('categories', 'c')
            ->addSelect(['c.id', 'name'])
            ->addSelect($query->subQuery($jobs, 'jobs'))
            ->addSelect($query->subQuery($affiliates, 'affiliates'));

        return $query->get();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $query = $this->query()
            ->nativeQuery();

        return $query->getConnection()->transaction(function () use ($query, $id) {
            $query->newQuery()
                ->table('jobs')
                ->where(['category_id' => $id])
                ->delete();

            $query->newQuery()
                ->table('affiliate_category')
                ->where(['category_id' => $id])
                ->delete();

            $query->newQuery()
                ->table('categories')
                ->where(['id' => $id])
                ->delete();
        });
    }
}
