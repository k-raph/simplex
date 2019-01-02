<?php

namespace Simplex\DataMapper;

use Simplex\DataMapper\Mapping\MetadataFactory;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Repository\Factory;

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
     * Setup the configuration class
     *
     * @param string $mappingDir
     * @return void
     */
    public static function setup(string $mappingDir): self
    {
        if (!is_dir($mappingDir)) {
            throw new \InvalidArgumentException(sprintf('Provided path must be a valid directory (%s)', $mappingDir));
        }

        $metafactory = new MetadataFactory();
        $repofactory = new Factory();
        $files = glob("$mappingDir/*.php");
        foreach ($files as $file) {
            $mapping = require $file;
            foreach ($mapping as $class => $meta) {
                $metadata = new EntityMetadata($class, $meta);
                $repository = $metadata->getRepositoryClass();
                $metafactory->setClassMetadata($class, $metadata);
                $repofactory->setClassRepository($class, new $repository);
            }
        }
        
        $config = new self;
        $config->metaFactory = $metafactory;
        $config->repoFactory = $repofactory;
        
        return $config;
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
