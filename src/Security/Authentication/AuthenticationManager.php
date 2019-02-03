<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02/02/2019
 * Time: 22:39
 */

namespace Simplex\Security\Authentication;


use Simplex\Security\Authentication\Provider\UserProviderInterface;
use Simplex\Security\Authentication\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationManager
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
     * @var SessionInterface
     */
    private $session;

    /**
     * AuthManager constructor.
     * @param UserProviderInterface $provider
     * @param SessionInterface $session
     */
    public function __construct(UserProviderInterface $provider, SessionInterface $session)
    {
        $this->provider = $provider;
        $this->session = $session;
    }

    /**
     * Checks for authenticated user within the request
     *
     * @param Request $request
     * @return bool
     */
    public function authenticate(Request $request): bool
    {
        $this->user = $this->check($request->getSession());

        return (bool)$this->user;
    }

    /**
     * Search for authenticated user within the session
     *
     * @param SessionInterface $session
     * @return UserInterface|null
     */
    protected function check(SessionInterface $session): ?UserInterface
    {
        $token = $session->get('auth.user');

        return $token
            ? $this->provider->refreshUser($token)
            : null;
    }

    /**
     * Performs a login using $credentials
     *
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials): bool
    {
        $user = $this->provider->loadUserByUsername($credentials['login']);

        if ($user && ($user->getPassword() === $credentials['password'])) {
            $this->user = $user;
            $this->session->set('auth.user', $user->getToken());
            return true;
        }

        return false;
    }

    /**
     * Performs a logout
     *
     * @return bool
     */
    public function logout(): bool
    {
        $this->provider->forget($this->getUser());
        $this->user = null;
        $this->session->remove('auth.user');
        return $this->session->migrate();
    }

    /**
     * Gets authenticated user
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if (!$this->user) {
            $this->user = $this->check($this->session);
        }

        return $this->user;
    }
}