<?php

namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Simplex\Renderer\TwigRenderer;


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
    public function __construct(PostTable $posts, TwigRenderer $renderer)
    {
        $this->posts = $posts;
        $this->view = $renderer;
    }
    
    /**
     * Show a single post
     *
     * @param integer $id
     * @return string
     */
    public function single(int $id)
    {
        return $this->view->render('@blog/show', ['post' => $this->posts->find($id)]);
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
                'posts' => $this->posts->getAll()
            ]);
    }

}