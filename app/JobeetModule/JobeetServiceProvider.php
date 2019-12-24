<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 19:54
 */

namespace App\JobeetModule;

use App\JobeetModule\Admin\Command\AffiliatesListCommand;
use App\JobeetModule\Admin\Command\CategoryCreateCommand;
use App\JobeetModule\Admin\Events\AffiliateActivationEvent;
use App\JobeetModule\Admin\Listener\AffiliateActivationMailer;
use App\JobeetModule\Entity\Affiliate;
use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use App\JobeetModule\Mapper\AffiliateMapper;
use App\JobeetModule\Mapper\CategoryMapper;
use App\JobeetModule\Mapper\JobMapper;
use App\JobeetModule\Repository\AffiliateRepository;
use Keiryo\EventManager\EventManagerInterface;
use Keiryo\Helper\Str;
use Keiryo\Queue\Contracts\QueueInterface;
use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouteCollection;
use Keiryo\Security\Authentication\StatelessAuthenticationManager;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;
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

        $container->get(EventManagerInterface::class)
            ->on(
                AffiliateActivationEvent::class,
                function (AffiliateActivationEvent $event) use ($container) {
                    return (new AffiliateActivationMailer())
                        ->handle(
                            $event,
                            $container->get(QueueInterface::class)
                        );
                }
            );
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
     * @param Configuration $configuration
     * @return mixed|void
     */
    public function configure(Configuration $configuration)
    {
        $configuration->load(__DIR__ . '/resources/config.yml', 'jobeet');
        $this->host = $configuration->get('app_host', 'localhost');
    }

    /**
     * @return array
     */
    public function getMappings(): array
    {
        return [
            'connection' => 'simplex',
            'mappings' => [
                Job::class => JobMapper::class,
                Category::class => CategoryMapper::class,
                Affiliate::class => AffiliateMapper::class
            ]
        ];
    }

    /**
     * @param TwigRenderer $renderer
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'jobeet');

        $renderer->getEnv()->addFilter(new TwigFilter('slug', function (string $string) {
            return Str::slugify($string);
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

    /**
     * @return string[]
     */
    public function getCommands(): array
    {
        return [
            CategoryCreateCommand::class,
            AffiliatesListCommand::class
        ];
    }
}
