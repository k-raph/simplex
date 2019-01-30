<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/01/2019
 * Time: 18:26
 */

namespace App\Blog\Action;


use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
use Simplex\DataMapper\EntityManager;
use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentAddAction
{

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param int $post_id
     * @param Request $request
     * @param EntityManager $entityManager
     * @return RedirectResponse
     */
    public function __invoke(int $post_id, Request $request, EntityManager $entityManager)
    {
        /** @var PostRepository $repo */
        $repo = $entityManager->getRepository(Post::class);
        $data = $request->request->all();
        if ($repo->exists($post_id) && $this->isValid($data)) {
            $comment = new Comment();
            $comment->setContent($data['content']);
            $comment->setCreatedAt(new \DateTime());
            $comment->setPost($post_id);
            $comment->setAuthor(1);

            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return new RedirectResponse($this->router->generate('post_show', ['id' => $post_id]));
    }

    /**
     * Validate input
     *
     * @param array $values
     * @return bool
     */
    private function isValid(array $values)
    {
        foreach ($values as $key => $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }
}