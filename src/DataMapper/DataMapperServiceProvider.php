<?php

namespace Simplex\DataMapper;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Database\DatabaseInterface;

class DataMapperServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    protected $provides = [
        EntityManager::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $db = $this->container->get(DatabaseInterface::class);
        $config = new Configuration(dirname(__DIR__).'/../resources/mappings');
        $em = new EntityManager($config, $db);

        $this->container->add(EntityManager::class, $em);
    }
}
