<?php

namespace Simplex\DataMapper;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
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
                ->getManager($this->container->get(Configuration::class)->get('database.default'));

            return $manager;
        });

        $this->container->add(UnitOfWork::class, function () {
            return $this->container
                ->get(EntityManager::class)
                ->getUnitOfWork();
        });
    }
}
