<?php

namespace App\Blog;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Extension\TwigTextExtension;
use App\Blog\Mapper\CommentMapper;
use App\Blog\Mapper\PostMapper;
use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;

class BlogModule extends AbstractModule
{

    /**
     * Constructor
     *
     * @param TwigRenderer $renderer
     * @param RouterInterface $router
     */
    public function __construct(TwigRenderer $renderer, RouterInterface $router)
    {
        $router->import(__DIR__ . '/config/routes.yml', ['prefix' => 'blog']);

        $renderer->getEnv()->addExtension(new TwigTextExtension());
        $renderer->addPath(__DIR__.'/views', 'blog');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'blog';
    }

    /**
     * @return array
     */
    public function getMappings(): array
    {
        return [
            Post::class => PostMapper::class,
            Comment::class => CommentMapper::class
        ];
    }

    /**
     * @param Configuration $configuration
     * @return mixed|void
     */
    public function configure(Configuration $configuration)
    {
        $configuration->load(__DIR__ . '/config/config.yml', 'blog');
    }
}