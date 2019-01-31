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
use Simplex\Session\SessionFlash;
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
     * @param Request $request
     * @param EntityManager $entityManager
     * @param SessionFlash $flash
     * @return RedirectResponse
     * @throws \Exception
     */
    public function __invoke(Request $request, EntityManager $entityManager, SessionFlash $flash)
    {
        /** @var PostRepository $repo */
        $id = $request->attributes->get('_route_params')['post_id'];
        $repo = $entityManager->getRepository(Post::class);
        $data = $request->request->all();
        if ($repo->exists($id) && $this->isValid($data)) {
            $comment = new Comment();
            $comment->setContent($data['content']);
            $comment->setCreatedAt(new \DateTime());
            $comment->setPost($id);
            $comment->setAuthor(1);

            $entityManager->persist($comment);
            $entityManager->flush();

            $flash->success('Your comment has been successfully added');
        }

        return new RedirectResponse($this->router->generate('post_show', ['id' => $id]));
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