<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\Blog\Action;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Simplex\DataMapper\EntityManager;
use Simplex\Renderer\TwigRenderer;

class ShowAction
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
     * @param integer $id
     * @param EntityManager $em
     * @return string
     */
    public function single(int $id, EntityManager $em)
    {
        $post = $em->getRepository(Post::class)->with('author')->find($id);
        return $this->view->render('@blog/show', compact('post'));
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
                'posts' => $em->getRepository(Post::class)->with('author')->findAll()
            ]);
    }
}
