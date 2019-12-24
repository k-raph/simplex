<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/02/2019
 * Time: 17:48
 */

namespace App\JobeetModule\Mapper;

use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use DateTime;
use Keiryo\Database\Exceptions\ResourceNotFoundException;
use Keiryo\DataMapper\IdentifiableInterface;
use Keiryo\DataMapper\Mapping\EntityMapper;

class JobMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'jobs';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return Job
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        if ($this->uow->getIdentityMap()->has(Job::class, $input['id'])) {
            return $this->uow->getIdentityMap()->get(Job::class, $input['id']);
        }

        $job = new Job($input['company'], $input['position']);

        if (isset($input['category_id'])) {
            /** @var CategoryMapper $mapper */
            $mapper = $this->uow->getEntityManager()->getMapper(Category::class);
            $category = $mapper->createEntity(['id' => $input['category_id'], 'name' => $input['category_name']]);

            $job->setCategory($category);
        }

        if (isset($input['is_public'])) {
            $job->setPublic((bool)$input['is_public']);
        }

        if (isset($input['type'])) {
            $job->setType(Job::TYPES[$input['type']]);
        }

        if (isset($input['created_at'])) {
            $job->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $input['created_at']));
        }

        if (isset($input['expires_at'])) {
            $job->setExpiresAt(DateTime::createFromFormat('Y-m-d H:i:s', $input['expires_at']));
        }

        foreach ([
                     'id', 'category', 'logo', 'url', 'application',
                     'description', 'public', 'token', 'email', 'location'] as $field) {
            if (isset($input[$field])) {
                $method = 'set' . ucfirst($field);
                $job->{$method}($input[$field]);
            }
        }

        $this->uow->getIdentityMap()->add($job, $job->getId());
        return $job;
    }

    /**
     * @param $id
     * @return IdentifiableInterface|null
     */
    public function find($id): ?IdentifiableInterface
    {
        $entity = $this->query('j')
            ->addSelect([
                'j.id', 'company', 'email', 'location', 'position', 'description',
                'application', 'type', 'logo', 'expires_at'])
            ->addSelect('is_public', 'public')
            ->addSelect('c.name', 'category_name')
            ->addSelect('c.id', 'category_id')
            ->where('j.id', $id)
            ->innerJoin(['categories', 'c'], 'j.category_id', 'c.id')
            ->first();

        if (null === $entity) {
            throw new ResourceNotFoundException();
        }

        return $entity;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Job $job
     * @return array
     */
    public function extract(IdentifiableInterface $job): array
    {
        return [
            'company' => $job->getCompany(),
            'type' => $job->getType(),
            'url' => $job->getUrl(),
            'position' => $job->getPosition(),
            'location' => $job->getLocation(),
            'email' => $job->getEmail(),
            'category_id' => $job->getCategory(),
            'description' => $job->getDescription(),
            'application' => $job->getApplication(),
            'is_public' => $job->isPublic(),
            'logo' => $job->getLogo(),
            'token' => $job->getToken(),
            'created_at' => $job->getCreatedAt()->format('Y-m-d H:i:s'),
            'expires_at' => $job->getExpiresAt() ? $job->getExpiresAt()->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Performs an entity update
     *
     * @param IdentifiableInterface|Job $entity
     * @return mixed
     */
    public function update(IdentifiableInterface $entity)
    {
        $entity->setType(Job::TYPES[$entity->getType()]);
        $changes = $this->uow->getChangeSet($entity);

        // Logic to check if category should be updated
        if (isset($changes['category']) && is_object($changes['category'])) {
            $category = $this->uow->getIdentityMap()->getOriginal($entity)->getCategory();
            $category = $category instanceof Category ? $category->getId() : $category;
            if ($category === $entity->getCategory()->getId()) {
                unset($changes['category']);
            } else {
                $changes['category'] = $entity->getCategory()->getId();
            }
        }

        $changes = $this->map($changes);

        if (!empty($changes)) {
            return $this->query()
                ->where('id', $entity->getId())
                ->update($changes);
        }
        return 0;
    }

    /**
     * Map props to field
     *
     * @param array $input
     * @return array
     */
    protected function map(array $input): array
    {
        $map = ['category' => 'category_id', 'public' => 'is_public'];
        foreach ($map as $key => $value) {
            if (isset($input[$key])) {
                $input[$map[$key]] = $input[$key];
                unset($input[$key]);
            }
        }

        if (isset($input['type'])) {
            $input['type'] = array_flip(Job::TYPES)[$input['type']];
        }

        if (isset($input['expiresAt'])) {
            $input['expires_at'] = $input['expiresAt']->format('Y-m-d H:i:s');
            unset($input['expiresAt']);
        }

        return $input;
    }

    /**
     * Performs an entity deletion
     *
     * @param IdentifiableInterface|Job $entity
     * @return mixed
     */
    public function delete(IdentifiableInterface $entity)
    {
        return $this->query()
            ->where(['id' => $entity->getId()])
            ->delete();
    }
}
