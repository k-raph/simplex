<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:28
 */

namespace App\BankuModule\DataSource\Mapper;

use App\BankuModule\Entity\Customer;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class CustomerMapper extends EntityMapper
{

    use PersonHydratorTrait;

    /**
     * @var string
     */
    protected $table = 'customers';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return IdentifiableInterface|Customer
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $customer = new Customer();
        $customer->setId($input['id']);
        $this->createPerson($customer, $input);

        $this->uow->getIdentityMap()->add($customer, $customer->getId());
        return $customer;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Customer $customer
     * @return array
     */
    public function extract(IdentifiableInterface $customer): array
    {
        $branch = $customer->getBranch();
        return array_merge([
            'id' => $customer->getId(),
            'date_of_birth' => $customer->getBirthDate()->format('Y-m-d H:i:s'),
            'joined_at' => $customer->getJoinDate()->format('Y-m-d H:i:s'),
            'branch_id' => $branch instanceof Branch ? $branch->getId() : $branch
        ], $this->extractPerson($customer));
    }
}
