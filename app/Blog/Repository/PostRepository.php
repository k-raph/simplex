<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29/01/2019
 * Time: 06:28
 */

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Blog\Mapper\PostMapper;
use Simplex\Database\Query\Builder;
use Simplex\DataMapper\QueryBuilder;
use Simplex\DataMapper\Repository\Repository;

class PostRepository extends Repository
{

    /**
     * @var PostMapper
     */
    private $mapper;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * PostRepository constructor.
     * @param PostMapper $mapper
     * @param CommentRepository $commentRepository
     */
    public function __construct(PostMapper $mapper, CommentRepository $commentRepository)
    {
        $this->mapper = $mapper;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function findAll(): array
    {
        return $this->mapper->findAll();
    }

    /**
     * @param mixed $id
     * @return object|null
     */
    public function find($id): ?object
    {
        /** @var Post $post */
        $post = $this->mapper->find($id);
        $post->setComments($this->commentRepository->findForPost($post));

        return $post;
    }

    /**
     * Gets result for given page
     *
     * @return Builder
     */
    public function queryForIndex(): Builder
    {
        return $this->mapper->buildSelect();
    }

    /**
     * Checks wether a post exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id)
    {
        return (bool)$this->query('p')
            ->where('p.id', $id)
            ->count();
    }

    /**
     * @param string|null $alias
     * @return QueryBuilder
     */
    protected function query(?string $alias = null): QueryBuilder
    {
        return $this->mapper->query($alias);
    }
}
