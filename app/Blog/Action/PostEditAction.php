<?php

namespace App\Blog\Action;

use Simplex\Renderer\TwigRenderer;
use App\Blog\Table\PostTable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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
            $data = $request->request->all();

            if ($this->isValid($data)) {
                $data['author_id'] = 1;
                $data['slug'] = 'my-post-slug-'.time();
                $this->posts->insert($data);
                return $data;
            } else {
                return 'Error';
            }
        }

        return $this->view->render('@blog/new_post');
    }

    public function update(int $id, Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            if ($this->isValid($data)) {
                $this->posts->update($id, $data);
                return json_encode($this->posts->find($id));
            } else {
                return 'Error';
            }
        }

        return $this->view->render('@blog/new_post', ['post' => $this->posts->find($id)]);
    }

    public function delete(int $id)
    {
        $this->posts->delete($id);
        return new Response('Deleted', 204);
    }

    private function isValid(array $data): bool
    {
        foreach($data as $key => $value) {
            if (!preg_match('#\w+#', $value))
                return false;
        }

        return true;
    }

}