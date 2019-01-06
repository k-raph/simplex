<?php

namespace Simplex\DataMapper\Mapping;

use Simplex\DataMapper\Repository\Repository;

class EntityMetadata
{
    
    /**
     * @var string
     */
    protected $className;

    /**
     * @var array
     */
    protected $datas;

    public function __construct(string $className, array $mapping)
    {
        $this->className = $className;
        $parts = explode('\\', $className);
        $this->datas = array_merge([
            'table' => sprintf('%ss', strtolower(array_pop($parts))),
            'repositoryClass' => Repository::class,
            'id' => 'id',
            'fields' => [],
        ], $mapping);
    }

    /**
     * Gets entity table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->datas['table'];
    }
    
    /**
     * Get entity identifier
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->datas['id'];
    }

    /**
     * Get entity's repository class name
     *
     * @return string
     */
    public function getRepositoryClass(): string
    {
        return $this->datas['repositoryClass'];
    }
    
    /**
     * Get entity's class name
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->className;
    }
    
    /**
     * Get fields name
     *
     * @return array
     */
    public function getNames(): array
    {
        return array_keys($this->datas['fields']);
    }
    
    /**
     * Get sql fields mapped names
     *
     * @return array
     */
    public function getSQLNames(): array
    {
        return array_map([$this, 'getSQLName'], $this->getNames());
    }
    
    /**
     * Get SQL's field name of the class provided field name
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getSQLName(string $name): ?string
    {
        if (!isset($this->datas['fields'][$name])) {
            return null;
        }

        $field = $this->datas['fields'][$name];
        return $field['column'] ?? $name;
    }
    
    /**
     * @return boolean
     */
    // public function hasRelations(): bool;
    
    /**
     * @return array
     */
    // public function getRelations(): array;
    
    /**
     * @param $name
     *
     * @return null|array
     */
    // public function getRelation($name);
    
    /**
     * Get provided column name's type
     *
     * @param $name
     *
     * @return Type
     */
    public function getColumnType(string $name): string
    {
        if (!isset($this->datas['fields'][$name])) {
            return null;
        }

        return $this->datas['fields'][$name]['type'] ?? 'string';
    }
    
    /**
     * Convert provided value to valid php type
     *
     * @param string $name
     * @param mixed $value
     *
     * @return mixed
     */
    public function toPhp(string $name, $value)
    {
    }
    

    /**
     * Gets custom persister class name
     *
     * @return string|null
     */
    public function customPersister(): ?string
    {
        return $this->datas['persisterClass'] ?? null;
    }
    /**
     * @return mixed
     */
    // public function getPrimaryColumns();
}
