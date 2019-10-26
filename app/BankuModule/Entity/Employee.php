<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 02:48
 */

namespace App\BankuModule\Entity;

use Simplex\DataMapper\IdentifiableInterface;

class Employee implements IdentifiableInterface
{

    use PersonTrait;

    /**
     * @var Branch|string
     */
    protected $branch;

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
}
