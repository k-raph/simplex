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

        $files = glob("$this->mappingDir/*.php");
        foreach ($files as $file) {
            $mapping = require $file;
            foreach ($mapping as $class => $meta) {
                $metadata = new EntityMetadata($class, $meta);
                $metafactory->setClassMetadata($class, $metadata);
            }
        }
        
        $this->metaFactory = $metafactory;
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
}
