<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/01/2019
 * Time: 18:26
 */

namespace App\BlogModule\Action;

use App\BlogModule\Entity\Comment;
use App\BlogModule\Entity\Post;
use DateTime;
use Keiryo\DataMapper\EntityManager;
use Keiryo\Routing\RouterInterface;
use Keiryo\Validation\Validator;
use Rakit\Validation\Validation;
use Simplex\Http\Session\SessionFlash;
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
        $id = $request->attributes->get('_route_params')['post_id'];
        $data = $request->request->all();

        $data = $this->validate($data)->getValidData();
        $exists = (bool)$entityManager
            ->getMapper(Post::class)
            ->query()
            ->where('id', $id)
            ->count();

        if ($exists) {
            $comment = new Comment();
            $comment->setContent($data['content']);
            $comment->setAuthor($data['pseudo']);
            $comment->setEmail($data['email']);
            $comment->setCreatedAt(new DateTime());
            $comment->setPostId($id);

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
