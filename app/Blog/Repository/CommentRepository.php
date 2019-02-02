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
            ->addSelect(['content', 'created_at', 'usr_pseudo'])
            ->where('post_id', $post->getId())
            ->get();

        return $result;
    }

}