<?php

namespace App\Blog\Action;

use Simplex\Renderer\TwigRenderer;
use App\Blog\Table\PostTable;
use Symfony\Component\HttpFoundation\Request;


class PostEditAction
{

    /**
     * Renderer instance
     *
     * @var TwigRenderer
     */
    private $view;

    /**
     * Post table
     *
     * @var PostTable
     */
    private $posts;

    public function __construct(TwigRenderer $renderer, PostTable $posts)
    {
        $this->view = $renderer;
        $this->posts = $posts;
    }

    public function add(Request $request)
    {
        if ($request->isMethod('POST')) {

        }

        return $this->view->render('@blog/new_post');
    }

    public function update()
    {
        if ($request->isMethod('POST')) {

        }

        return $this->view->render('@blog/edit_post');
    }

    public function delete()
    {
        return 'Deleted';
    }

}