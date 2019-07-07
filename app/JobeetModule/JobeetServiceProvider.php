<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 19:54
 */

namespace App\JobeetModule;

use App\JobeetModule\Admin\Events\AffiliateActivationEvent;
use App\JobeetModule\Admin\Listener\AffiliateActivationMailer;
use App\JobeetModule\Entity\Affiliate;
use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use App\JobeetModule\Mapper\AffiliateMapper;
use App\JobeetModule\Mapper\CategoryMapper;
use App\JobeetModule\Mapper\JobMapper;
use App\JobeetModule\Repository\AffiliateRepository;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\EventManager\EventManagerInterface;
use Simplex\Module\AbstractModule;
use Simplex\Queue\Contracts\QueueInterface;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;
use Simplex\Security\Authentication\StatelessAuthenticationManager;
use Twig\TwigFilter;

class JobeetServiceProvider extends AbstractModule
{
    /**
     * @var string
     */
    private $host;

    /**
     * JobeetServiceProvider constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $container->add(StatelessAuthenticationManager::class)
            ->addArgument(AffiliateRepository::class);

        $this->host = $container->get(Configuration::class)
            ->get('app_host', 'localhost');

        $container->get(EventManagerInterface::class)
            ->on(
                AffiliateActivationEvent::class,
                function (AffiliateActivationEvent $event) use ($container) {
                    return (new AffiliateActivationMailer())
                        ->handle(
                            $event,
                            $container->get(QueueInterface::class
                            )
                        );
            });
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

    /**
     * @param TwigRenderer $renderer
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'jobeet');

        $renderer->getEnv()->addFilter(new TwigFilter('slug', function (string $string) {
            $string = preg_replace('~\W+~', '-', $string);
            $string = trim($string, '-');

            return strtolower($string);
        }));
    }

    /**
     * @param RouteCollection $collection
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml', [
            'host' => 'jobeet.' . $this->host
        ]);
    }

    /**
     * @param RouteCollection $collection
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function getAdminRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/Admin/routes.yml', [
            'prefix' => 'jobeet',
            'name_prefix' => 'admin_jobeet_'
        ]);
    }
}