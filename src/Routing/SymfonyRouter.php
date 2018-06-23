<?php

namespace Simplex\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class SymfonyRouter implements RouterInterface
{

    /**
     * Route collection builder
     *
     * @var RouteCollectionBuilder
     */
    private $builder;

    /**
     * Cnstructor
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->builder =  new RouteCollectionBuilder($loader);
    }

    /**
     * {@inheritDoc}
     */
    public function import($from, $prefix = '/')
    {
        $this->builder->import($from, $prefix);
    }

    /**
     * {@inheritDoc}
     */
    public function match($methods, $path, $controller, $name = null)
    {
        $this->builder->add($path, $controller, $name)
            ->setMethods(explode('|', $methods));
    }

    /**
     * Matches a GET request
     *
     * @param string $path
     * @param string $controller
     * @param string $name
     * @return void
     */
    public function get($path, $controller, $name = null)
    {
        return $this->match('GET', $path, $controller, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Request $request)
    {
        try {
            $context = new RequestContext();
            $matcher = new UrlMatcher($this->getCollection(), $context->fromRequest($request));
            $parameters = $matcher->matchRequest($request);
            $request->attributes->add($parameters);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
            return new Route(
                $request->attributes->get('_route'),
                $request->attributes->get('_controller'),
                $request->attributes->get('_route_params')
            );
        } catch (ResourceNotFoundException $e) {
            $message = sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo());

            if ($referer = $request->headers->get('referer')) {
                $message .= sprintf(' (from "%s")', $referer);
            }

            throw new ResourceNotFoundException($message, 404);
        } catch (MethodNotAllowedException $e) {
        $message = sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getPathInfo(), /*implode(', ', $e->getAllowedMethods())*/ 'GET');

            throw new MethodNotAllowedException(/*$e->getAllowedMethods()*/ ['GET'],$message);
        }
    }

    /**
     * Build route collection
     *
     * @return RouteCollection
     */
    protected function getCollection()
    {
        return $this->builder->build();
    }
}