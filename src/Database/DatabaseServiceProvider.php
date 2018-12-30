<?php

namespace Simplex\Database;

use League\Container\ServiceProvider\AbstractServiceProvider;

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
        $options = $this->container->get('config')['database'] ?? [];
        $config = new Configuration($options);

        $this->container->add(DatabaseInterface::class, new Database($config));
    }
}
