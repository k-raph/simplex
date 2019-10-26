<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:01
 */

namespace App\BankuModule\Entity;

use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\IdentifiableTrait;

class Account implements IdentifiableInterface
{

    use IdentifiableTrait;

    const STATUS_ENABLED = 'ENABLED';

    const STATUS_DISABLED = 'DISABLED';

    const TYPE_SAVING = 'SAVING';

    const TYPE_CHECKING = 'CHECKING';

    const TYPE_CURRENT = 'CURRENT';

    /**
     * @var string
     */
    protected $no;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $balance;

    /**
     * @var float
     */
    protected $interest_rate;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var Branch|string
     */
    protected $branch;

    /**
     * @var Customer|string
     */
    protected $owner;

    /**
     * Account constructor.
     * @param string $no
     */
    public function __construct(string $no)
    {
        $this->setNo($no);
    }

    /**
     * @return string
     */
    public function getNo(): string
    {
        return $this->no;
    }

    /**
     * @param string $no
     */
    public function setNo(string $no): void
    {
        $this->no = $no;
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
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @param int $balance
     */
    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getInterestRate(): float
    {
        return $this->interest_rate;
    }

    /**
     * @param float $interest_rate
     */
    public function setInterestRate(float $interest_rate): void
    {
        $this->interest_rate = $interest_rate;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Branch|string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param Branch|string $branch
     */
    public function setBranch($branch): void
    {
        $this->branch = $branch;
    }

    /**
     * @return Customer|string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Customer|string $owner
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @param int $amount
     */
    public function deposit(int $amount)
    {
        $this->balance += $amount;
    }

    /**
     * @param int $amount
     */
    public function withdraw(int $amount)
    {
        if ($amount > $this->balance) {
            throw new \RuntimeException("You don't have enough money");
        }

        $this->balance -= $amount;
    }
}
