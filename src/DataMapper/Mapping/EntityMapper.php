<?php

namespace Simplex\DataMapper\Mapping;

use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Proxy\Proxy;
use Simplex\DataMapper\Proxy\ProxyFactory;

class EntityMapper
{
    /**
     * @var EntityMetadata
     */
    protected $metadata;

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityMetadata $metadata, EntityManager $manager)
    {
        $this->metadata = $metadata;
        $this->em = $manager;
        $this->proxyFactory = $manager->getProxyFactory();
    }

    /**
     * Create an entity based on given data
     *
     * @param array $data
     * @return object
     */
    public function createEntity(array $data): object
    {
        $props = $this->mapToProps($data);

        $proxy = $this->createProxy();
        $proxy->hydrate($props);

        return $this->postCreateEntity($proxy->reveal());
    }

    /**
     * Extracts fields data from given entity
     *
     * @param object $entity
     * @return array
     */
    public function extract(object $entity): array
    {
        $proxy = $this->proxyFactory->wrap($entity);
        $props = $proxy->toArray();
        $result = [];

        foreach ($this->getMappings() as $prop => $column) {
            $result[$column] = $props[$prop] ?? null;
        }

        return $result;
    }

    /**
     * Hydrates an object with provided data
     *
     * @param object $entity
     * @param array $data
     * @return void
     */
    public function hydrate(object $entity, array $data)
    {
        $proxy = $this->proxyFactory
            ->wrap($entity);

        $proxy->hydrate($this->mapToProps($data));

        return $entity;
    }

    /**
     * Get the value of a field from provided object
     *
     * @param object $entity
     * @param string $field
     * @return mixed
     */
    public function getField(object $entity, string $field)
    {
        return $this->proxyFactory
            ->wrap($entity)
            ->getField($field);
    }

    /**
     * Get mapped class metadata
     *
     * @return EntityMetadata
     */
    public function getMetadata(): EntityMetadata
    {
        return $this->metadata;
    }

    /**
     * Get fields mappings
     *
     * @return array
     */
    protected function getMappings(): array
    {
        return array_combine(
            $this->metadata->getNames(),
            $this->metadata->getSQLNames()
        );
    }

    /**
     * Maps a data array to props array
     *
     * @param array $data
     * @return array
     */
    protected function mapToProps(array $data): array
    {
        $props = [];
        $fields = $this->getMappings();

        foreach ($fields as $prop => $column) {
            if (!isset($data[$column])) {
                continue;
            }
            $props[$prop] = $data[$column];
        }

        return $props;
    }

    /**
     * Creates a proxy instance
     *
     * @return Proxy
     */
    protected function createProxy(): Proxy
    {
        return $this->proxyFactory->create($this->metadata->getEntityClass());
    }

    /**
     * Method to invoke before persist
     *
     * @param object $entity
     * @return object
     */
    public function prePersist(object $entity)
    {
        $fields = [];
        $proxy = $this->proxyFactory->wrap($entity);
        foreach ($this->metadata->getNames() as $field) {
            $type = $this->metadata->getColumnType($field);
            switch ($type) {
                case 'string':
                case 'int':
                    $fields[$field] = $proxy->getField($field);
                    break;
                case 'datetime':
                    $value = $proxy->getField($field);
                    $value = $value instanceof \DateTime
                        ? $value->format('d-m-Y H:i:s')
                        : $value;
                    $fields[$field] = $value;
                    break;
            }
        }

        $proxy->hydrate($fields);
        return $proxy->reveal();
    }

    /**
     * Invoked after creating entity
     *
     * @param object $entity
     * @return object
     */
    protected function postCreateEntity(object $entity)
    {
        $fields = [];
        $proxy = $this->proxyFactory->wrap($entity);
        foreach ($this->metadata->getNames() as $field) {
            $type = $this->metadata->getColumnType($field);
            switch ($type) {
                case 'datetime':
                    $fields[$field] = \DateTime::createFromFormat('d-m-Y H:i:s', $proxy->getField($field));
                    break;
            }
        }

        $proxy->hydrate($fields);
        return $proxy->reveal();
    }
}
