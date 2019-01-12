<?php

namespace Simplex\DataMapper\Mapping;

class MetadataFactory
{

    /**
     * Registered metadatas
     *
     * @var EntityMetadata[]
     */
    protected $metadatas = [];

    /**
     * Add a class metadata instance
     *
     * @param string $className
     * @param EntityMetadata $metadata
     * @return void
     */
    public function setClassMetadata(string $className, EntityMetadata $metadata)
    {
        $this->metadatas[$className] = $metadata;
    }

    /**
     * Get metadata instance associed to a classname
     *
     * @param string $className
     * @return EntityMetadata|null
     */
    public function getClassMetadata(string $className): ?EntityMetadata
    {
        return $this->metadatas[$className] ?? null;
    }

    /**
     * Check entity class metadata existence
     *
     * @param string $className
     * @return boolean
     */
    public function hasMetadataFor(string $className): bool
    {
        return isset($this->metadatas[$className]);
    }
}
