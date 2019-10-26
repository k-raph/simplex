<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 02:53
 */

namespace App\BankuModule\Entity;

use Simplex\DataMapper\IdentifiableInterface;

class Customer implements IdentifiableInterface
{

    use PersonTrait;

    /**
     * @var \DateTime
     */
    protected $birthDate;

    /**
     * @var Branch|string
     */
    protected $branch;

    /**
     * @var \DateTime
     */
    protected $joinDate;

    /**
     * @return \DateTime
     */
    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
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
     * @return \DateTime
     */
    public function getJoinDate(): \DateTime
    {
        return $this->joinDate;
    }

    /**
     * @param \DateTime $joinDate
     */
    public function setJoinDate(\DateTime $joinDate): void
    {
        $this->joinDate = $joinDate;
    }
}
