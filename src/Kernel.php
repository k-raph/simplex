<?php

namespace Simplex;

use Keiryo\EventManager\EventManager;
use Keiryo\EventManager\EventManagerInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Simplex\Configuration\Configuration;
use Simplex\Events\KernelBootEvent;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;

class Kernel
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * Kernel is booted?
     *
     * @var boolean
     */
    protected $booted = false;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $container = new Container();
        $container->defaultToShared();
        $container->delegate((new ReflectionContainer)->cacheResolutions(true));
        $container->add(ContainerInterface::class, $container);

        $this->eventManager = new EventManager();
        $container->add(EventManagerInterface::class, $this->eventManager);

        $this->container = $container;
        $this->configure();
    }

    /**
     * Load config files
     *
     * @return void
     */
    public function configure(): void
    {
        try {
            $root = dirname(__DIR__);
            $path = "$root/config";
            $locator = new FileLocator($path);

            $config = new Configuration([
                'root' => $root,
                'resources' => "$root/resources"
            ]);

            foreach (glob("$path/*") as $file) {
                $info = pathinfo($file);
                $namespace = $info['filename'];

                $config->load(
                    $locator->locate($info['basename']),
                    ('config' === $namespace || 'bootstrap' === $namespace)
                        ? null
                        : $namespace
                );
            }
            $this->container->add(Configuration::class, $config);
        } catch (FileLocatorFileNotFoundException $e) {
            throw new RuntimeException('Config files not found!');
        }
    }

    /**
     * Bootstrap container
     *
     * @return void
     */
    private function bootstrap(): void
    {
        $providers = $this->container
            ->get(Configuration::class)
            ->get('providers', []);

        foreach ($providers as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }

    /**
     * Boot the kernel
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->bootstrap();
        $config = $this->container->get(Configuration::class);
        $this->eventManager->dispatch(new KernelBootEvent($config))->getConfiguration();

        $this->booted = true;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
