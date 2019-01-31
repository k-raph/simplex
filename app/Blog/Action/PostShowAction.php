<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\Blog\Action;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use Simplex\DataMapper\EntityManager;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;

class PostShowAction
{
    /**
     * Renderer engine
     *
     * @var TwigRenderer
     */
    private $view;

    /**
     * Constructor
     *
     * @param TwigRenderer $view
     */
    public function __construct(TwigRenderer $view)
    {
        $this->view = $view;
    }

    /**
     * Show a single post
     *
     * @param Request $request
     * @param EntityManager $em
     * @return string
     */
    public function single(Request $request, EntityManager $em)
    {
        $id = $request->attributes->get('_route_params')['id'];
        $flash = $request->getSession()->getFlashBag();

        $post = $em->getRepository(Post::class)->find($id);
        $comments = $em->getRepository(Comment::class)->findForPost($post);
        $post->setComments($comments);

        return $this->view->render('@blog/show', compact('post', 'flash'));
    }

    /**
     * Show all posts on the blog
     *
     * @param EntityManager $em
     * @return string
     */
    public function all(EntityManager $em)
    {
        return $this->view
            ->render('@blog/index', [
                'posts' => $em->getRepository(Post::class)->findAll()
            ]);
    }
}
