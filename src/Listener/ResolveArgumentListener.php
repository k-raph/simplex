<?php

namespace Simplex\Listener;

use League\Event\AbstractListener;
use League\Event\EventInterface;
use Psr\Container\ContainerInterface;
use League\Container\Argument\ArgumentResolverTrait;
use League\Container\ContainerAwareTrait;
use League\Container\Argument\ArgumentResolverInterface;
use ReflectionMethod;
use ReflectionFunction;

class ResolveArgumentListener extends AbstractListener implements ArgumentResolverInterface
{

    use ArgumentResolverTrait, ContainerAwareTrait;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(EventInterface $event)
    {
        $controller = $event->getController();
        $reflected = \is_array($controller)
            ? new ReflectionMethod($controller[0], $controller[1])
            : new ReflectionFunction($controller);

        $args = $this->reflectArguments($reflected, $event->getParams());
        $event->setParams($args);

        return $event;
    }

}