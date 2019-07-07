<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06/07/2019
 * Time: 11:54
 */

namespace App\Providers;


use Kached\Connection;
use Kached\Kache;
use Kached\Socket\SocketInterface;
use Kached\Socket\StreamSocket;
use League\Container\ServiceProvider\AbstractServiceProvider;

class KacheServiceProvider extends AbstractServiceProvider
{

    protected $provides = [
        SocketInterface::class,
        Kache::class,
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
        $this->container->add(SocketInterface::class, function () {
            return new StreamSocket('127.0.0.1', 1397);
        });
        $this->container->add(Kache::class, function () {
            $connection = new Connection($this->container->get(SocketInterface::class));
            return new Kache($connection);
        });
    }
}