<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/03/2019
 * Time: 12:38
 */

namespace Simplex\Security\Authentication;

use Simplex\Security\Authentication\Provider\UserProviderInterface;
use Simplex\Security\Authentication\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class StatelessAuthenticationManager
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var UserProviderInterface
     */
    private $provider;

    /**
     * AuthManager constructor.
     * @param UserProviderInterface $provider
     */
    public function __construct(UserProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Checks for authenticated user within the request
     *
     * @param Request $request
     * @return bool
     */
    public function authenticate(Request $request): bool
    {
        $token = $request->headers->get('Authorization', 'simplex');
        $this->user = $this->provider->loadUserByUsername($token);

        return (bool)$this->user;
    }

    /**
     * Gets authenticated user
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }
}
