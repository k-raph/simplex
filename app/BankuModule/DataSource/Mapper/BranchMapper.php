<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:12
 */

namespace App\BankuModule\DataSource\Mapper;

use App\BankuModule\Entity\Branch;
use App\BankuModule\Entity\Employee;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class BranchMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'branches';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return IdentifiableInterface|Branch
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $branch = new Branch();
        foreach (['id', 'name', 'city', 'country', 'phone', 'manager'] as $field) {
            if (isset($input[$field])) {
                $branch->{'set' . ucfirst($field)}($input[$field]);
            }
        }

        return $branch;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Branch $branch
     * @return array
     */
    public function extract(IdentifiableInterface $branch): array
    {
        $extract = [
            'id' => $branch->getId(),
            'name' => $branch->getName(),
            'city' => $branch->getCity(),
            'country' => $branch->getCountry(),
            'phone' => $branch->getPhone()
        ];
        $manager = $branch->getManager();
        $extract['manager_id'] = $manager instanceof Employee
            ? $manager->getId()
            : $manager;

        return $extract;
    }
}
