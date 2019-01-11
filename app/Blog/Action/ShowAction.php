<?php

namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Simplex\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Simplex\Routing\RouterInterface;
use Simplex\Database\DatabaseInterface;
use Simplex\DataMapper\EntityManager;
use App\Blog\Entity\Post;
use App\Blog\Entity\User;
use Symfony\Component\HttpFoundation\Response;

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
    public function single(int $id, EntityManager $em)
    {
        $post = $em->find(Post::class, $id);
        return $this->view->render('@blog/show', compact('post'));
    }

    /**
     * Show all posts on the blog
     *
     * @return string
     */
    public function all(EntityManager $em)
    {
        var_dump($em->find(User::class, 1));
        return new Response();
        // return $this->view
        //     ->render('@blog/index', [
        //         'posts' => $em->getRepository(Post::class)->findAll()
        //     ]);
    }
}
