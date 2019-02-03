<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 02:18
 */

namespace Simplex\Security\Authentication\Provider;


use Simplex\Database\DatabaseInterface;
use Simplex\Database\Query\Builder;
use Simplex\Security\Authentication\User\User;
use Simplex\Security\Authentication\User\UserInterface;

class DatabaseUserProvider implements UserProviderInterface
{

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var string
     */
    private $field = 'email';

    /**
     * DatabaseUserProvider constructor.
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
        $this->builder = $database->getQueryBuilder()->table('users');
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     *
     */
    public function loadUserByUsername(string $username): ?UserInterface
    {
        $result = $this->builder
            ->where($this->field, $username)
            ->addSelect([$this->field, 'password', 'session_token'])
            ->first();

        if ($result) {
            return new User($result[$this->field], $result['session_token'], $result['password']);
        }

        return null;
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param string $sessionToken
     * @return UserInterface|null
     */
    public function refreshUser(string $sessionToken): ?UserInterface
    {
        $result = $this->builder
            ->where('session_token', $sessionToken)
            ->addSelect([$this->field, 'session_token'])
            ->first();

        return $result
            ? new User($result[$this->field], $result['session_token'])
            : null;
    }

    /**
     * @param string $field
     */
    public function setFieldName(string $field): void
    {
        $this->field = $field;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param UserInterface $user
     */
    public function forget(UserInterface $user)
    {
        $this->builder
            ->where([
                $this->field => $user->getUsername(),
                'session_token' => $user->getToken()
            ])
            ->update([
                'session_token' => base64_encode(random_bytes(20))
            ]);
    }
}