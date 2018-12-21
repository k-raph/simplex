<?php

namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Simplex\Routing\RouterInterface;
use Simplex\Database\Query\Builder;

class ShowAction
{

    /**
     * Post table
     *
     * @var PostTable
     */
    private $posts;

    /**
     * Renderer engine
     *
     * @var TwigRenderer
     */
    private $view;

    /**
     * Constructor
     *
     * @param PostTable $posts
     * @param TwigRenderer $renderer
     */
    public function __construct(TwigRenderer $view)
    {
        $this->view = $view;
    }
    
    /**
     * Show a single post
     *
     * @param integer $id
     * @return string
     */
    public function single(int $id, Builder $query)
    {
        return $this->view->render('@blog/show', ['post' => $query->table('posts')->findOrFail($id)]);
    }

    /**
     * Show all posts on the blog
     *
     * @return string
     */
    public function all()
    {
        return $this->view
            ->render('@blog/index', [
                'posts' => []
            ]);
    }

}