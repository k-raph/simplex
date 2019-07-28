<?php

namespace Simplex\DataMapper;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Database\DatabaseManager;

class DataMapperServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    protected $provides = [
        DataMapper::class,
        //EntityManager::class,
        UnitOfWork::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $db = $this->container->get(DatabaseManager::class);

        $this->container->add(DataMapper::class, function () use ($db) {
            return new DataMapper($db);
        });

        $this->container->add(UnitOfWork::class, function () {
            return $this->container
                ->get(EntityManager::class)
                ->getUnitOfWork();
        });
    }
}
