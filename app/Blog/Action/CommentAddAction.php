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
use Rakit\Validation\Validation;
use Simplex\DataMapper\EntityManager;
use Simplex\Http\Session\SessionFlash;
use Simplex\Routing\RouterInterface;
use Simplex\Validation\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentAddAction
{

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Validator
     */
    private $validator;

    public function __construct(RouterInterface $router, Validator $validator)
    {
        $this->router = $router;
        $this->validator = $validator;
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

        $data = $this->validate($data)->getValidData();

        if ($repo->exists($id)) {
            $comment = new Comment();
            $comment->setContent($data['content']);
            $comment->setAuthor($data['pseudo']);
            $comment->setEmail($data['email']);
            $comment->setCreatedAt(new \DateTime());
            $comment->setPost($id);

            $entityManager->persist($comment);
            $entityManager->flush();

            $flash->success('Your comment has been successfully added');
        }

        return new RedirectResponse($this->router->generate('post_show', ['id' => $id]));
    }

    /**
     * @param array $input
     * @return \Rakit\Validation\Validation
     */
    private function validate(array $input): Validation
    {
        return $this->validator->validate($input, [
            'pseudo' => 'required|alpha_dash',
            'email' => 'required|email',
            'content' => 'required',
        ]);
    }
}