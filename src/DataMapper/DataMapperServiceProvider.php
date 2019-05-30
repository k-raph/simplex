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
        EntityManager::class,
        UnitOfWork::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $db = $this->container->get(DatabaseManager::class);

        $dataMapper = new DataMapper($db);
        $em = $dataMapper->getManager();

        $this->container->add(DataMapper::class, $dataMapper);
        $this->container->add(EntityManager::class, $em);
        $this->container->add(UnitOfWork::class, $em->getUnitOfWork());
    }
}
