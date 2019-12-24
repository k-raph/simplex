<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 15:09
 */

namespace Simplex\Http;

use Keiryo\EventManager\EventManagerInterface;
use Keiryo\Http\Pipeline;
use Psr\Container\ContainerInterface;
use Simplex\Configuration\Configuration;
use Simplex\Events\KernelRequestEvent;
use Simplex\Events\KernelResponseEvent;
use Simplex\Kernel as Simplex;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tracy\Debugger;

class Kernel
{

    /**
     * @var Pipeline
     */
    protected $pipeline;

    /**
     * @var Simplex
     */
    private $kernel;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Kernel constructor.
     * @param Simplex $kernel
     */
    public function __construct(Simplex $kernel)
    {
        $this->kernel = $kernel;
        $this->pipeline = new Pipeline();
        $this->container = $kernel->getContainer();
        $this->container->add(Pipeline::class, $this->pipeline);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $this->bootstrap();

        $request->enableHttpMethodParameterOverride();

        /** @var EventManagerInterface $eventManager */
        $eventManager = $this->container->get(EventManagerInterface::class);
        $event = $eventManager->dispatch(new KernelRequestEvent($request));

        $response = $event->isPropagationStopped()
            ? $event->getResponse()
            : $this->pipeline->handle($request);

        /** @var KernelResponseEvent $event */
        $event = $eventManager->dispatch(new KernelResponseEvent($response));
        return $event->getResponse();
    }

    /**
     * Bootstrap the kernel
     */
    protected function bootstrap()
    {
        $config = $this->container->get(Configuration::class);

        if ('debug' === $config->get('env')) {
            Debugger::enable();
        }

        $this->kernel->boot();

        // Register middlewares
        $pipes = $config->get('routing.middlewares.global', []);
        foreach ($pipes as $key => $middleware) {
            if (is_array($middleware)) {
                continue;
            }
            $this->pipeline->pipe($this->container->get($middleware));
        }
    }

    /**
     * Terminate request handling
     *
     * @param Response $response
     * @param Request $request
     * @return void
     */
    public function terminate(Response $response, Request $request)
    {
        $response->prepare($request)->send();
    }
}
