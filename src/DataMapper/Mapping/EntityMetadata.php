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

    /**
     * EntityMetadata constructor.
     * @param string $className
     * @param array $mapping
     */
    public function __construct(string $className, array $mapping)
    {
        $this->className = $className;
        $parts = explode('\\', $className);

        // Parse the fields
        $fields = $mapping['fields'];
        $mapping['fields'] = [];
        foreach ($fields as $key => $field) {
            if (is_integer($key)) {
                $key = $field;
                $field = [
                    'type' => 'string'
                ];
            }

            $mapping['fields'][$key] = $field;
        }

        $this->datas = array_merge([
            'table' => sprintf('%ss', strtolower(array_pop($parts))),
            'repositoryClass' => Repository::class,
            'id' => 'id',
            'fields' => [],
            'relations' => []
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
     * Get field name from sql name
     *
     * @param string $sqlName
     * @return string|null
     */
    public function getName(string $sqlName): ?string
    {
        foreach ($this->datas['fields'] as $field => $map) {
            $column = $map['column'] ?? $field;
            if ($sqlName === $column) {
                return $field;
            }
        }

        return null;
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
     * Get provided column name's type
     *
     * @param $name
     *
     * @return Type
     */
    public function getColumnType(string $name): ?string
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
}
