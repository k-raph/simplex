<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17/02/2019
 * Time: 10:59
 */

namespace App\JobeetModule\Repository;

use App\JobeetModule\Mapper\AffiliateMapper;
use Simplex\DataMapper\QueryBuilder;
use Simplex\DataMapper\Repository\Repository;
use Simplex\Security\Authentication\Provider\UserProviderInterface;
use Simplex\Security\Authentication\User\UserInterface;

class AffiliateRepository extends Repository implements UserProviderInterface
{

    /**
     * @var AffiliateMapper
     */
    protected $mapper;

    public function __construct(AffiliateMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Gets an entry by its primary primary key
     *
     * @param mixed $id
     * @return object|null
     */
    public function find($id): ?object
    {
        return $this->mapper->find($id);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->query()
            ->addSelect(['id', 'name', 'email'])
            ->where(['is_active' => false])
            ->get();
    }

    /**
     * @param string|null $alias
     * @return QueryBuilder
     */
    protected function query(?string $alias = null): QueryBuilder
    {
        return $this->mapper->query($alias);
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $token The username
     *
     * @return UserInterface|null
     *
     */
    public function loadUserByUsername(string $token): ?UserInterface
    {
        return $this->query()
            ->where(['token' => $token, 'is_active' => true])
            ->first();
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param string $token
     * @return UserInterface
     */
    public function refreshUser(string $token): ?UserInterface
    {
        return null;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param UserInterface $user
     */
    public function forget(UserInterface $user)
    {
        // TODO: Implement forget() method.
    }
}
