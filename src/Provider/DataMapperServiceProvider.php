<?php

namespace Simplex\Provider;

use Keiryo\Database\DatabaseManager;
use Keiryo\DataMapper\DataMapper;
use Keiryo\DataMapper\EntityManager;
use Keiryo\DataMapper\Mapping\MappingRegistry;
use Keiryo\DataMapper\UnitOfWork;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;

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
                ->getManager(
                    $this->container
                        ->get(Configuration::class)
                        ->get('database.default')
                );

            return $manager;
        });

        $this->container->add(UnitOfWork::class, function () {
            return $this->container
                ->get(EntityManager::class)
                ->getUnitOfWork();
        });
    }
}
