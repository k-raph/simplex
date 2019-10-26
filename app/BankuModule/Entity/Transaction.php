<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:07
 */

namespace App\BankuModule\Entity;

use DateTime;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\IdentifiableTrait;

class Transaction implements IdentifiableInterface
{

    use IdentifiableTrait;

    const DEPOSIT = 'DEPOSIT';

    const WITHDRAW = 'WITHDRAW';

    /**
     * @var Account|string
     */
    protected $account;

    /**
     * @var DateTime
     */
    protected $time;

    /**
     * @var Employee|string
     */
    protected $employee;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $amount;

    /**
     * Transaction constructor.
     * @param Account|string $account
     */
    public function __construct($account)
    {
        $this->setAccount($this->account);
    }

    /**
     * @return Account|string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account|string $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     */
    public function setTime(DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * @return Employee|string
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param Employee|string $employee
     */
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }
}
