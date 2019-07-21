<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:13
 */

namespace App\JobeetModule\Repository;

use App\JobeetModule\Entity\Job;
use App\JobeetModule\Mapper\JobMapper;
use Simplex\Database\Exceptions\ResourceNotFoundException;
use Simplex\DataMapper\QueryBuilder;
use Simplex\DataMapper\Repository\Repository;

class JobRepository extends Repository
{

    /**
     * @var JobMapper
     */
    private $mapper;

    public function __construct(JobMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Gets active jobs filtered for given categories
     *
     * @param array $ids
     * @return array
     * @throws \Throwable
     */
    public function getActiveForCategories(array $ids)
    {
        return $this->activeQuery()
            ->whereIn('category_id', $ids)
            ->get();
    }

    /**
     * Special query to retrieve jobs based on given affiliate id
     *
     * @param int $id
     * @param array $categories
     * @param int|null $limit
     * @return array
     * @throws \Throwable
     */
    public function getActiveForAffiliate(int $id, array $categories = [], ?int $limit = null): array
    {
        $query = $this->query()->nativeQuery();
        $c_ids = $query->table('affiliate_category', 'p')
            ->addSelect('p.category_id')
            ->where('p.affiliate_id', $id);

        $query = $query->newQuery()->table('categories', 'c')
            ->addSelect('c.id')
            ->whereIn('c.id', $query->subQuery($c_ids));

        if (!empty($categories)) {
            $query = $query->whereIn('c.name', array_map('ucfirst', $categories));
        }

        $categories = $query->get();

        $ids = array_map(function (array $row) {
            return $row['id'];
        }, $categories);

        $query = $query->newQuery()->table('jobs', 'j')
            ->addSelect(['j.id', 'j.company', 'j.type', 'j.position', 'j.location'])
            ->addSelect('c.name', 'category')
            ->whereIn('j.category_id', $ids)
            ->innerJoin(['categories', 'c'], 'j.category_id', 'c.id');

        if ($limit) {
            $query = $query->offset(0)->limit($limit);
        }

        return $query
            ->where('j.expires_at', '>', (new \DateTime())->format('Y-m-d H:i:s'))
            ->where('is_public', true)
            ->orderBy('j.id')
            ->get();
    }

    /**
     * Gets active jobs
     *
     * @return array
     * @throws \Throwable
     */
    public function getActive()
    {
        return $this->activeQuery()->get();
    }

    /**
     * Query for active jobs
     *
     * @param int|null $id
     * @return QueryBuilder
     * @throws \Exception
     */
    public function activeQuery(?int $id = null): QueryBuilder
    {
        $query = $this->query('j')
            ->addSelect(['j.id', 'company', 'location', 'position'])
            ->where('j.expires_at', '>', (new \DateTime())->format('Y-m-d H:i:s'))
            ->orderBy('created_at', 'DESC');

        if ($id) {
            $query = $query->where('j.category_id', $id);
        } else {
            $query = $query
                ->addSelect('c.name', 'category')
                ->innerJoin(['categories', 'c'], 'j.category_id', 'c.id');
        }

        return $query;
    }

    /**
     * @param mixed $id
     * @return object|null
     * @throws \Exception
     */
    public function find($id): ?object
    {
        $job = $this->query('j')
            ->addSelect(['j.id', 'company', 'location', 'position', 'description', 'application', 'type'])
            ->addSelect('c.name', 'category_id')
            ->where('j.id', $id)
            ->where('j.expires_at', '>', (new \DateTime())->format('Y-m-d H:i:s'))
            ->innerJoin(['categories', 'c'], 'j.category_id', 'c.id')
            ->first();

        $job->setType(Job::TYPES[$job->getType()]);

        return $job;
    }

    /**
     * Gets a job with provided token
     *
     * @param string $token
     * @return Job
     */
    public function findByToken(string $token): Job
    {
        /** @var Job $job */
        $job = $this->findOneBy(['token' => $token]);

        if (null === $job) {
            throw new ResourceNotFoundException();
        }

        return $job;
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
     * @return JobMapper
     */
    public function getMapper(): JobMapper
    {
        return $this->mapper;
    }
}
