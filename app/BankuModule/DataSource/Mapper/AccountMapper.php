<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:43
 */

namespace App\BankuModule\DataSource\Mapper;

use App\BankuModule\Entity\Account;
use App\BankuModule\Entity\Branch;
use App\BankuModule\Entity\Customer;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class AccountMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'accounts';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return IdentifiableInterface
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $account = new Account($input['number']);
        if (isset($input['interest_rate'])) {
            $account->setInterestRate($input['interest_rate']);
        }
        foreach (['id', 'type', 'balance', 'status'] as $field) {
            if (isset($input[$field])) {
                $account->{'set' . ucfirst($field)}($input[$field]);
            }
        }

        $this->uow->getIdentityMap()->add($account, $account->getId());
        return $account;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Account $account
     * @return array
     */
    public function extract(IdentifiableInterface $account): array
    {
        $branch = $account->getBranch();
        $owner = $account->getOwner();
        return [
            'id' => $account->getId(),
            'number' => $account->getNo(),
            'type' => $account->getType(),
            'balance' => $account->getBalance(),
            'interest_rate' => $account->getInterestRate(),
            'status' => $account->getStatus(),
            'branch_id' => $branch instanceof Branch ? $branch->getId() : $branch,
            'customer_id' => $owner instanceof Customer ? $owner->getId() : $owner
        ];
    }
}
