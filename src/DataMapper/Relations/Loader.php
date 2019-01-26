<?php

namespace Simplex\DataMapper\Relations;

use Simplex\DataMapper\EntityManager;

class Loader
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $manager)
    {
        $this->em = $manager;
    }

    /**
     * Build a relation instance based on given configuration
     *
     * @param string $className
     * @param array $config
     * @return RelationInterface
     * @throws \Exception
     */
    public function build(string $className, array $config): RelationInterface
    {
        $type = strtolower($config['type']);
        switch ($type) {
            case 'onetomany':
                return new OneToMany($className, $config['target'], $config['targetField'], $config['field']);
                break;
            case 'manytoone':
                $field = $this->em
                    ->getMapperFor($className)
                    ->getMetadata()
                    ->getSQLName($config['name']);
                return new ManyToOne($className, $config['target'], $config['targetField'], $field);
                break;
            default:
                throw new \Exception('This point should never be reached');
                break;
        }
    }
}
