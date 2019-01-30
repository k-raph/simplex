<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/01/2019
 * Time: 17:46
 */

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use Simplex\DataMapper\Repository\Repository;

class CommentRepository extends Repository
{

    /**
     * @param Post $post
     * @return array
     */
    public function findForPost(Post $post)
    {
        $result = $this->query('c')
            ->addSelect(['content', 'created_at'])
            ->addSelect('u.username', 'author_id')
            ->innerJoin(['users', 'u'], 'c.author_id', 'u.id')
            ->where('post_id', $post->getId())
            ->get();

        return array_map([$this->mapper, 'createEntity'], $result);
    }

}