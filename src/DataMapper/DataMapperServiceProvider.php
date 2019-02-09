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
        EntityManager::class,
        UnitOfWork::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $db = $this->container->get(DatabaseInterface::class);

        $em = new EntityManager($db);
        $this->container->add(EntityManager::class, $em);
        $this->container->add(UnitOfWork::class, $em->getUnitOfWork());
    }
}
