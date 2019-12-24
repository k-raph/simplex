<?php

namespace App\BlogModule;

use App\BlogModule\Entity\Comment;
use App\BlogModule\Entity\Post;
use App\BlogModule\Extension\TwigTextExtension;
use App\BlogModule\Mapper\CommentMapper;
use App\BlogModule\Mapper\PostMapper;
use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouteCollection;
use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;

class BlogModule extends AbstractModule
{

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
            'connection' => 'simplex',
            'mappings' => [
                Post::class => PostMapper::class,
                Comment::class => CommentMapper::class
            ]
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

    /**
     * @param TwigRenderer $renderer
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->getEnv()->addExtension(new TwigTextExtension());
        $renderer->addPath(__DIR__ . '/views', 'blog');
    }

    /**
     * @param RouteCollection $collection
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/config/routes.yml', ['prefix' => 'blog']);
    }
}
