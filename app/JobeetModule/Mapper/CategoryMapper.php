<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/02/2019
 * Time: 18:27
 */

namespace App\JobeetModule\Mapper;


use App\JobeetModule\Entity\Category;
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
     * @return object
     */
    public function createEntity(array $input): object
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
     * @param object $entity
     * @return array
     */
    public function extract(object $entity): array
    {
        // TODO: Implement extract() method.
    }

    /**
     * Performs an entity update
     *
     * @param object $entity
     * @return mixed
     */
    public function update(object $entity)
    {
        // TODO: Implement update() method.
    }

    /**
     * Performs an entity deletion
     *
     * @param object $entity
     * @return mixed
     */
    public function delete(object $entity)
    {
        // TODO: Implement delete() method.
    }
}