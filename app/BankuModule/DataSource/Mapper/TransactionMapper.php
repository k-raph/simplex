<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:55
 */

namespace App\BankuModule\DataSource\Mapper;

use App\BankuModule\Entity\Employee;
use App\BankuModule\Entity\Transaction;
use DateTime;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class TransactionMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'transactions';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return IdentifiableInterface|Transaction
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $transaction = new Transaction($input['account_number']);
        foreach (['id', 'type', 'amount'] as $field) {
            if (isset($input[$field])) {
                $transaction->{'set' . ucfirst($field)}($input[$field]);
            }
        }

        if (isset($input['timestamp'])) {
            $transaction->setTime((new DateTime())->setTimestamp($input['timestamp']));
        }

        $this->uow->getIdentityMap()->add($transaction, $transaction->getId());
        return $transaction;
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface|Transaction $transaction
     * @return array
     */
    public function extract(IdentifiableInterface $transaction): array
    {
        $account = $transaction->getAccount();
        $employee = $transaction->getEmployee();
        return [
            'id' => $transaction->getId(),
            'type' => $transaction->getType(),
            'account_number' => $account, //$account instanceof Account ? $account->getNo() : $account,
            'timestamp' => $transaction->getTime()->getTimestamp(),
            'amount' => $transaction->getAmount(),
            'employee_id' => $employee instanceof Employee ? $employee->getId() : $employee
        ];
    }
}
