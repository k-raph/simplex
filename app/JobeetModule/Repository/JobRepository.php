<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:13
 */

namespace App\JobeetModule\Repository;

use App\JobeetModule\Entity\Job;
use Simplex\DataMapper\Repository\Repository;

class JobRepository extends Repository
{

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
     * @param bool $forView
     * @return Job
     */
    public function findByToken(string $token, bool $forView = true): Job
    {
        /** @var Job $job */
        $job = $this->findOneBy(['token' => $token]);

        if (null === $job) {
            throw new ResourceNotFoundException();
        }

        try {
            $job->getExpiresAt();
        } catch (\TypeError $e) {
            $job->setExpiresAt(null);
        }

        if ($forView) {
            $job->setType(Job::TYPES[$job->getType()]);
        }
        return $job;
    }
}