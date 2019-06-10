<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/06/2019
 * Time: 19:23
 */

namespace Simplex\Exception;


use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Simplex\Event\EventManagerInterface;
use Simplex\Exception\Listener\JsonExceptionListener;
use Simplex\Exception\Listener\WebExceptionListener;

class ExceptionHandlerProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    /**
     * @inheritdoc
     */
    public function boot()
    {
        /** @var EventManagerInterface $eventManager */
        $eventManager = $this->container->get(EventManagerInterface::class);

        $eventManager->on('kernel.exception', new JsonExceptionListener(), 100);
        $eventManager->on('kernel.exception', new WebExceptionListener($eventManager));
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
    }
}