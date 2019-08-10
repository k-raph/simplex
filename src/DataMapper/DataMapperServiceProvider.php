<?php

namespace Simplex\DataMapper;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Database\DatabaseManager;
use Simplex\DataMapper\Mapping\MappingRegistry;

class DataMapperServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritDoc}
     */
    protected $provides = [
        DataMapper::class,
        EntityManager::class,
        UnitOfWork::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->container->add(DataMapper::class)
            ->addArgument(DatabaseManager::class)
            ->addArgument(MappingRegistry::class);

        $this->container->add(EntityManager::class, function () {
            /** @var EntityManager $manager */
            $manager = $this->container->get(DataMapper::class)
                ->getManager();

            return $manager;
        });

        $this->container->add(UnitOfWork::class, function () {
            return $this->container
                ->get(EntityManager::class)
                ->getUnitOfWork();
        });
    }
}
