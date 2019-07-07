<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\Blog\Action;

use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
use Simplex\Configuration\Configuration;
use Simplex\Database\Exceptions\ResourceNotFoundException;
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
     * @param PostRepository $repository
     * @return string
     */
    public function single(int $id, PostRepository $repository)
    {
        /** @var Post $post */
        $post = $repository->find($id);

        return $this->view->render('@blog/show', compact('post'));
    }

    /**
     * Show all posts on the blog
     *
     * @param Request $request
     * @param PostRepository $repository
     * @param RouterInterface $router
     * @return string
     * @throws ResourceNotFoundException
     */
    public function all(Request $request, PostRepository $repository, RouterInterface $router)
    {
        $page = $request->query->getInt('page', 1);

        $query = $repository->queryForIndex();

        $paginator = new Paginator();
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
