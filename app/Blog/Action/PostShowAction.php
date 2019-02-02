<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\Blog\Action;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use Simplex\Configuration\Configuration;
use Simplex\Database\Exceptions\ResourceNotFoundException;
use Simplex\DataMapper\EntityManager;
use Simplex\Pagination\Paginator;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
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
     * @var int
     */
    private $postPerPage;

    /**
     * Constructor
     *
     * @param TwigRenderer $view
     * @param Configuration $configuration
     */
    public function __construct(TwigRenderer $view, Configuration $configuration)
    {
        $this->view = $view;
        $this->postPerPage = $configuration->get('blog.posts_per_page');
    }

    /**
     * Show a single post
     *
     * @param int $id
     * @param EntityManager $em
     * @return string
     */
    public function single(int $id, EntityManager $em)
    {
        $post = $em->getRepository(Post::class)->find($id);

        $comments = $em->getRepository(Comment::class)->findForPost($post);
        $post->setComments($comments);

        return $this->view->render('@blog/show', compact('post'));
    }

    /**
     * Show all posts on the blog
     *
     * @param Request $request
     * @param EntityManager $em
     * @param Paginator $paginator
     * @param RouterInterface $router
     * @return string
     */
    public function all(Request $request, EntityManager $em, Paginator $paginator, RouterInterface $router)
    {
        $page = $request->query->getInt('page', 1);

        $query = $em->getRepository(Post::class)->queryForIndex();
        $paginator
            ->withUrl($router->generate('blog_index'))
            ->paginate($query, $page, $this->postPerPage);

        if ($page > $paginator->lastPage()) {
            throw new ResourceNotFoundException();
        }

        return $this->view
            ->render('@blog/index', [
                'posts' => $paginator->getItems(),
                'pages' => $paginator
            ]);
    }
}
