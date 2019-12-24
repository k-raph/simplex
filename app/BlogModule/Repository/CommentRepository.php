<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/01/2019
 * Time: 17:46
 */

namespace App\BlogModule\Repository;

use App\BlogModule\Entity\Post;
use App\BlogModule\Mapper\CommentMapper;
use Keiryo\DataMapper\QueryBuilder;
use Keiryo\DataMapper\Repository\Repository;

class CommentRepository extends Repository
{

    /**
     * @var CommentMapper
     */
    private $mapper;

    /**
     * CommentRepository constructor.
     * @param CommentMapper $mapper
     */
    public function __construct(CommentMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param Post $post
     * @return array
     */
    public function findForPost(Post $post)
    {
        $result = $this->query('c')
            ->addSelect(['content', 'created_at', 'usr_pseudo'])
            ->where('post_id', $post->getId())
            ->get();

        return $result;
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
    }
}
