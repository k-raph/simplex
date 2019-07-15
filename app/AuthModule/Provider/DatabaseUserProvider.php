<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/07/2019
 * Time: 21:13
 */

namespace App\AuthModule\Provider;


use App\AuthModule\Entity\User;
use Simplex\Security\Authentication\Provider\DatabaseUserProvider as BaseUserProvider;

class DatabaseUserProvider extends BaseUserProvider
{

    /**
     * @param User $user
     * @return int
     */
    public function insert(User $user)
    {
        return $this->builder
            ->insert([
                'username' => $user->getUsername(),
                'session_token' => $user->getToken(),
                'password' => $user->getPassword(),
                'email' => $user->getEmail()
            ]);
    }

}