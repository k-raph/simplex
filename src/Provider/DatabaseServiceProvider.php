<?php

namespace Simplex\Provider;

use Keiryo\Database\Configuration as DatabaseConfiguration;
use Keiryo\Database\DatabaseInterface;
use Keiryo\Database\DatabaseManager;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    protected $provides = [
        DatabaseInterface::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->container->add(DatabaseManager::class, function () {
            $options = $this->container
                ->get(Configuration::class)
                ->get('database', []);
            $config = new DatabaseConfiguration($options);
            return new DatabaseManager($config);
        });

        $this->container->add(DatabaseInterface::class, function () {
            $manager = $this->container->get(DatabaseManager::class);
            return $manager->getDatabase('default');
        });
    }
}
