<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:20
 */

namespace App\BankuModule\DataSource\Mapper;

use App\BankuModule\Entity\Branch;
use App\BankuModule\Entity\Employee;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class EmployeeMapper extends EntityMapper
{

    use PersonHydratorTrait;

    /**
     * @var string
     */
    protected $table = 'employees';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return IdentifiableInterface|Employee
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $employee = new Employee();
        $employee->setId($input['id']);
        $this->createPerson($employee, $input);

        $this->uow->getIdentityMap()->add($employee, $employee->getId());
        return $employee;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Employee $employee
     * @return array
     */
    public function extract(IdentifiableInterface $employee): array
    {
        $branch = $employee->getBranch();
        return array_merge([
            'id' => $employee->getId(),
            'branch_id' => $branch instanceof Branch ? $branch->getId() : $branch
        ], $this->extractPerson($employee));
    }
}
