<?php

namespace Simplex\Listener;

use League\Event\AbstractListener;
use League\Event\EventInterface;
use Psr\Container\ContainerInterface;
use Simplex\Event\ControllerEvent;


class ResolveControllerListener extends AbstractListener
{

    /**
     * Container
     *
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        
    }
    
    public function handle(EventInterface $event)
    {
        $event->setController($this->getController($event));
        // $event->setParams($this->getArguments($event));

        return $event;
    }

    /**
     * Makes the controller callable
     *
     * @param ControllerEvent $event
     * @return callable
     */
    private function getController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_string($controller)) {
            if (strpos($controller, '::') !== false) {
                $controller = explode('::', $controller);
            } elseif (strpos($controller, ':') !== false) {
                var_dump($controller);
                $controller = explode(':', $controller);
            } elseif (method_exists($controller, '__invoke')) {
                $controller = $this->container->get($controller);
            }
        }
        
        if (is_array($controller)) {
            $controller[0] = is_object($controller[0])
                ? $controller[0]
                : $this->container->get($controller[0]);
        }

        return $controller;
    }

}