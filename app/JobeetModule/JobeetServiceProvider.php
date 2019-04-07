<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 19:54
 */

namespace App\JobeetModule;

use App\JobeetModule\Entity\Affiliate;
use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use App\JobeetModule\Mapper\AffiliateMapper;
use App\JobeetModule\Mapper\CategoryMapper;
use App\JobeetModule\Mapper\JobMapper;
use App\JobeetModule\Repository\AffiliateRepository;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use Simplex\Security\Authentication\StatelessAuthenticationManager;
use Twig\TwigFilter;

class JobeetServiceProvider extends AbstractModule
{

    /**
     * JobeetServiceProvider constructor.
     * @param RouterInterface $router
     * @param TwigRenderer $renderer
     * @param Configuration $config
     * @param ContainerInterface $container
     */
    public function __construct(RouterInterface $router, TwigRenderer $renderer, Configuration $config, ContainerInterface $container)
    {
        $renderer->addPath(__DIR__ . '/views', 'jobeet');

        $renderer->getEnv()->addFilter(new TwigFilter('slug', function (string $string) {
            $string = preg_replace('~\W+~', '-', $string);
            $string = trim($string, '-');

            return strtolower($string);
        }));

        $container->add(StatelessAuthenticationManager::class)
            ->addArgument(AffiliateRepository::class);

        $router->import(__DIR__ . '/resources/routes.yml', [
            'host' => 'jobeet.' . $config->get('app_host', 'localhost')
        ]);
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'jobeet';
    }

    /**
     * @return array
     */
    public function getMappings(): array
    {
        return [
            Job::class => JobMapper::class,
            Category::class => CategoryMapper::class,
            Affiliate::class => AffiliateMapper::class
        ];
    }
}