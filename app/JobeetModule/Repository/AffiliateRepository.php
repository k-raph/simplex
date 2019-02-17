<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17/02/2019
 * Time: 10:59
 */

namespace App\JobeetModule\Repository;


use App\JobeetModule\Mapper\AffiliateMapper;
use Simplex\DataMapper\QueryBuilder;
use Simplex\DataMapper\Repository\Repository;

class AffiliateRepository extends Repository
{

    /**
     * @var AffiliateMapper
     */
    private $mapper;

    public function __construct(AffiliateMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Gets an entry by its primary primary key
     *
     * @param mixed $id
     * @return object|null
     */
    public function find($id): ?object
    {
        return $this->mapper->find($id);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->query()
            ->addSelect(['id', 'name', 'email'])
            ->where(['is_active' => false])
            ->get();
    }

    /**
     * @param string|null $alias
     * @return QueryBuilder
     */
    protected function query(?string $alias = null): QueryBuilder
    {
        return $this->mapper->query($alias);
    }
}