<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/02/2019
 * Time: 18:27
 */

namespace App\JobeetModule\Mapper;

use App\JobeetModule\Entity\Category;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class CategoryMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'categories';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return IdentifiableInterface
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $category = new Category($input['name']);
        foreach (['id', 'slug'] as $field) {
            if (isset($input[$field])) {
                $category->{'set' . ucfirst($field)}($input[$field]);
            }
        }

        $this->uow->getIdentityMap()->add($category, $category->getId());
        return $category;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Category $entity
     * @return array
     */
    public function extract(IdentifiableInterface $entity): array
    {
        return [
            'name' => $entity->getName(),
            'slug' => $entity->getSlug()
        ];
    }

    /**
     * Performs an entity update
     *
     * @param IdentifiableInterface|Category $entity
     * @return mixed
     */
    public function update(IdentifiableInterface $entity)
    {
        $changes = $this->uow->getChangeSet($entity);

        if (!empty($changes)) {
            return $this->query()
                ->where('id', $entity->getId())
                ->update($changes);
        }
    }

    /**
     * Performs an entity deletion
     *
     * @param IdentifiableInterface $entity
     * @return mixed
     */
    public function delete(IdentifiableInterface $entity)
    {
        // TODO
    }
}
