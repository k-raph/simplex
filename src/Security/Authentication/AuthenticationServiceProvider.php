<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 04:00
 */

namespace Simplex\Security\Authentication;


use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Configuration\Configuration;
use Simplex\Database\DatabaseInterface;
use Simplex\Http\CookieStorage;
use Simplex\Security\Authentication\Provider\DatabaseUserProvider;
use Simplex\Security\Authentication\Provider\UserProviderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationServiceProvider extends AbstractServiceProvider
{

    /**
     * @var array
     */
    protected $provides = [
        UserProviderInterface::class,
        AuthenticationManager::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        /** @var Configuration $config */
        $config = $this->container->get(Configuration::class);
        $field = $config->get('auth.login_field', 'email');
        $provider = $config->get('auth.users.provider', 'database');
        $params = $config->get("auth.providers.$provider", []);

        if ('database' === $provider) {
            $this->container->add(DatabaseUserProvider::class)
                ->addArgument(DatabaseInterface::class)
                ->addArgument($params['table'] ?? 'users')
                ->addArgument($field);
        }

        /** @var UserProviderInterface $provider */
        $provider = $this->container->get($params['class']);

        $loginPath = $config->get('app_host', 'localhost') . $config->get('auth.login_path', '/login');

        $this->container->add(UserProviderInterface::class, $provider);
        $this->container->add(AuthenticationManager::class)
            ->addArgument(UserProviderInterface::class)
            ->addArgument(SessionInterface::class)
            ->addArgument(CookieStorage::class)
            ->addMethodCall('setLoginPath', [$loginPath]);
    }
}