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

    public function loadRelation(string $className, array $config, array $data = [])
    {
        $rel = $this->build($className, $config);

        $mapper = $this->em->getMapperFor($rel->getTarget());
        $meta = $mapper->getMetadata();
        $uow = $this->em->getUnitOfWork();
        
        $query = $this->em->getConnection()->getQueryBuilder();
        $result = $query
            ->table($meta->getTableName())
            ->where([
                $rel->getTargetField() => $data[$rel->getOwnerField()] ?? null
            ])
            ->get();
        $result = array_map(function ($data) use ($mapper) {
            return $mapper->createEntity($data);
        }, $result);

        return $result;
    }

    protected function build(string $className, array $config)
    {
        $type = strtolower($config['type']);
        switch ($type) {
            case 'onetomany':
                return new OneToMany($className, $config['target'], $config['targetField'], $config['field']);
                break;
            default:
                throw new \Exception('This point should never be reached');
                break;
        }
    }
}
