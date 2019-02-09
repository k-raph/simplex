<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/02/2019
 * Time: 03:16
 */

namespace App\Blog\Mapper;


use App\Blog\Entity\Post;
use Simplex\Database\Exceptions\ResourceNotFoundException;
use Simplex\DataMapper\Mapping\EntityMapper;
use Simplex\DataMapper\QueryBuilder;

class PostMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->buildSelect()->get();
    }

    /**
     * Select query builder helper
     *
     * @return QueryBuilder
     */
    public function buildSelect(): QueryBuilder
    {
        return $this->query('p')
            ->addSelect(['p.id', 'title', 'slug', 'content'])
            ->addSelect('u.username', 'author')
            ->innerJoin(['users', 'u'], 'p.author_id', 'u.id');
    }

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return object
     */
    public function createEntity(array $input): object
    {
        $post = new Post();
        foreach ($input as $field => $value) {
            $method = 'set' . ucfirst($field);
            if (method_exists($post, $method)) {
                call_user_func([$post, $method], $value);
            }
        }

        $this->uow->getIdentityMap()->add($post, $post->getId());
        return $post;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param Post $post
     * @return array
     */
    public function extract(object $post): array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'slug' => $post->getSlug(),
            'author_id' => $post->getAuthor()
        ];
    }

    /**
     * Gets an entity by its primary key
     *
     * @param $id
     * @return object
     */
    public function find($id): object
    {
        $post = $this->buildSelect()
            ->where('p.id', $id)
            ->first();

        if (!$post) {
            throw new ResourceNotFoundException();
        }

        return $post;
    }

    /**
     * Performs an entity update
     *
     * @param Post $post
     * @return mixed
     */
    public function update(object $post)
    {
        $changes = $this->uow->getChangeSet($post);
        return $this->query()
            ->where('id', $post->getId())
            ->update($changes);
    }

    /**
     * Performs an entity deletion
     *
     * @param Post $post
     * @return mixed
     */
    public function delete(object $post)
    {
        return $this->query()
            ->where('id', $post->getId())
            ->delete();
    }
}