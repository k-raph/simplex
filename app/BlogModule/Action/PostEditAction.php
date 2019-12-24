<?php

namespace App\BlogModule\Action;

use App\BlogModule\Entity\Post;
use Keiryo\DataMapper\EntityManager;
use Keiryo\Renderer\Twig\TwigRenderer;
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
     * PostEditAction constructor.
     * @param TwigRenderer $renderer
     */
    public function __construct(TwigRenderer $renderer)
    {
        $this->view = $renderer;
    }

    public function add(Request $request, EntityManager $em)
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            if ($this->isValid($data)) {
                $post = new Post();
                $post->setContent($data['content']);
                $post->setTitle($data['title']);
                $post->setSlug('my-post-slug-' . time());
                $post->setAuthor(1);

                $data['author_id'] = 1;
                $data['slug'] = 'my-post-slug-'.time();
                $em->persist($post);
                $em->flush();
                return $post;
            } else {
                return 'Error';
            }
        }

        return $this->view->render('@blog/new_post');
    }

    public function update(int $id, Request $request, EntityManager $manager)
    {
        $post = $manager->find(Post::class, $id);

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            if ($this->isValid($data)) {
                $post->setContent($data['content']);
                $post->setTitle($data['title']);
                $manager->persist($post);
                $manager->flush();
                return json_encode($post);
            } else {
                return 'Error';
            }
        }

        return $this->view->render('@blog/new_post', compact('post'));
    }

    public function delete(int $id, EntityManager $em)
    {
        $em->getRepository(Post::class)
            ->remove($id);
        return new Response('Deleted', 204);
    }

    private function isValid(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (!preg_match('#\w+#', $value)) {
                return false;
            }
        }

        return true;
    }
}
