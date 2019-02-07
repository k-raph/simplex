<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:26
 */

namespace App\JobeetModule\Repository;

use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use Simplex\DataMapper\Repository\Repository;

class CategoryRepository extends Repository
{

    /**
     * Gets categories with active jobs
     *
     * @return array
     * @throws \Exception
     */
    public function getWithActiveJobs()
    {
        $categories = $this->findAll();

        $ids = array_map(function (Category $category) {
            return $category->getId();
        }, $categories);

        $manager = $this->query()->getManager();

        $jobs = $manager->createQueryBuilder(Job::class)
            ->newQuery('j')
            ->addSelect(['j.id', 'company', 'location', 'position', 'category_id'])
            ->addSelect('c.name', 'category_id')
            ->whereIn('category_id', $ids)
            ->where('j.expires_at', '>', (new \DateTime())->format('Y-m-d H:i:s'))
            ->innerJoin(['categories', 'c'], 'j.category_id', 'c.id')
            ->orderBy('created_at', 'DESC')
            ->get();

        foreach ($categories as $category) {
            $current = array_filter($jobs, function (Job $job) use ($category) {
                return $category->getName() === $job->getCategory();
            });

            $category->setJobs($current);
        }

        return $categories;
    }

}