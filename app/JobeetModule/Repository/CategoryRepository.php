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
use App\JobeetModule\Mapper\CategoryMapper;
use Simplex\DataMapper\QueryBuilder;
use Simplex\DataMapper\Repository\Repository;

class CategoryRepository extends Repository
{

    /**
     * @var CategoryMapper
     */
    private $mapper;

    /**
     * @var JobRepository
     */
    private $jobs;

    public function __construct(CategoryMapper $mapper, JobRepository $jobs)
    {
        $this->mapper = $mapper;
        $this->jobs = $jobs;
    }

    /**
     * Gets categories with active jobs
     *
     * @return array
     * @throws \Exception
     */
    public function getWithActiveJobs()
    {
        $categories = $this->mapper->findAll();

        $ids = array_map(function (Category $category) {
            return $category->getId();
        }, $categories);


        $jobs = $this->jobs->getActiveForCategories($ids);

        foreach ($categories as $category) {
            $current = array_filter($jobs, function (Job $job) use ($category) {
                return $category->getName() === $job->getCategory();
            });

            $category->setJobs($current);
        }

        return $categories;
    }

    /**
     * @param string|null $alias
     * @return QueryBuilder
     */
    protected function query(?string $alias = null): QueryBuilder
    {
        return $this->mapper->query($alias);
    }

    /**
     * Gets an entry by its primary primary key
     *
     * @param mixed $id
     * @return object|null
     */
    public function find($id): ?object
    {
        return $this->mapper->find($id);
    }

    /**
     * @return array
     */
    public function getAllForForm(): array
    {
        return $this->query()
            ->nativeQuery()
            ->table('categories', 'c')
            ->addSelect('id', 'slug')
            ->addSelect('name')
            ->get();
    }
}
