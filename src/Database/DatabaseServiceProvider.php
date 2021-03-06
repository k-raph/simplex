<?php

namespace Simplex\Database;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
use Simplex\Database\Configuration as DatabaseConfiguration;

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
