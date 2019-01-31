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
        $options = $this->container
            ->get(Configuration::class)
            ->get('database', []);
        $config = new DatabaseConfiguration($options);

        $this->container->add(DatabaseInterface::class, new Database($config));
    }
}
