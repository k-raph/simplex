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
     * @return EntityMetadata
     */
    public function getClassMetadata(string $className): EntityMetadata
    {
        $meta = $this->metadatas[$className] ?? null;
        
        if (!$meta) {
            throw new \UnexpectedValueException(sprintf('Metadata for class %s not found', $className));
        }

        return $meta;
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
