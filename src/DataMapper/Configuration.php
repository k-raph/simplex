<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Mapping\MetadataFactory;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Repository\Factory;
use Simplex\DataMapper\Proxy\ProxyFactory;
use Simplex\Database\DatabaseInterface;
use Simplex\DataMapper\Persistence\DatabasePersister;

class Configuration
{
    /**
     * @var MetadataFactory
     */
    protected $metaFactory;

    /**
     * @var Factory
     */
    protected $repoFactory;

    /**
     * Mapping directory
     *
     * @var string
     */
    protected $mappingDir;

    public function __construct(string $mappingDir)
    {
        if (!is_dir($mappingDir)) {
            throw new \InvalidArgumentException(sprintf('Provided path must be a valid directory (%s)', $mappingDir));
        }

        $this->mappingDir = $mappingDir;
    }

    /**
     * Setup the configuration class
     *
     * @param string $mappingDir
     * @return void
     */
    public function setup(EntityManager $manager)
    {
        $metafactory = new MetadataFactory();
        $repofactory = new Factory();
        $proxyFactory = new ProxyFactory($metafactory);

        $files = glob("$this->mappingDir/*.php");
        foreach ($files as $file) {
            $mapping = require $file;
            foreach ($mapping as $class => $meta) {
                $metadata = new EntityMetadata($class, $meta);
                $repository = $metadata->getRepositoryClass();
                $persister = new DatabasePersister($manager->getConnection()->getQueryBuilder(), $metadata, $proxyFactory);

                $metafactory->setClassMetadata($class, $metadata);
                $repofactory->setClassRepository($class, new $repository($metadata, $persister, $proxyFactory));
            }
        }
        
        $this->metaFactory = $metafactory;
        $this->repoFactory = $repofactory;
    }

    /**
     * Retrieves metadata factory
     *
     * @return MetadataFactory
     */
    public function getMetadataFactory(): MetadataFactory
    {
        return $this->metaFactory;
    }

    /**
     * Retrieves repository factory
     *
     * @return Factory
     */
    public function getRepositoryFactory(): Factory
    {
        return $this->repoFactory;
    }
}
