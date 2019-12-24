<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/07/2019
 * Time: 20:05
 */

namespace Simplex\Provider;

use Kached\Kache;
use Keiryo\Queue\Contracts\QueueInterface;
use Keiryo\Queue\QueueManager;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;

class QueueServiceProvider extends AbstractServiceProvider
{

    protected $provides = [
        QueueManager::class,
        QueueInterface::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->add(QueueManager::class, function () {
            $config = $this->container
                ->get(Configuration::class)
                ->get('queue', []);

            $manager = new QueueManager();
            $manager->setDefault($config['default']);

            $resolvers = $this->getResolvers();
            foreach ($config['connections'] as $connection => $params) {
                if (array_key_exists($connection, $resolvers)) {
                    $manager->register($connection, $resolvers[$connection]);
                }
            }

            return $manager;
        });

        $this->container->add(QueueInterface::class, function () {
            return $this->container->get(QueueManager::class)->connection();
        });
    }

    /**
     * @return array
     */
    private function getResolvers(): array
    {
        return [
            'kached' => function () {
                return new KacheQueue($this->container->get(Kache::class));
            }
        ];
    }
}
