<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/07/2019
 * Time: 21:21
 */

namespace App\AuthModule\Entity;


use Simplex\Security\Authentication\User\User as BaseUser;

class User extends BaseUser
{

    /**
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


}