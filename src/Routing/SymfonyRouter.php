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
use Symfony\Component\Routing\Generator\UrlGenerator;

class SymfonyRouter implements RouterInterface
{

    /**
     * Route collection builder
     *
     * @var RouteCollectionBuilder
     */
    private $builder;

    /**
     * @var RouteCollection
     */
    private $collection;

    /**
     * @var RequestContext
     */
    private $context;

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
        $this->builder->import($from, $prefix, 'yaml');
    }

    /**
     * {@inheritDoc}
     */
    public function match($methods, $path, $controller, $name = null)
    {
        $this->builder
            ->add($path, $controller, $name)
            ->setMethods(explode('|', $methods));
    }

    public function group($prefix, \Closure $factory)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $name, array $parameters = []): string
    {
        return (new UrlGenerator($this->getCollection(), $this->context ?? new RequestContext()))->generate($name, $parameters);
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
            $collection = $this->getCollection();
            $this->context = $context = (new RequestContext())->fromRequest($request);
            $matcher = new UrlMatcher($collection, $context);
            $parameters = $matcher->matchRequest($request);
            
            $route = new Route($parameters['_route'], $parameters['_controller']);
            
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
            $route->setParameters($parameters);

            return $route;
        } catch (ResourceNotFoundException $e) {
            $message = sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo());

            if ($referer = $request->headers->get('referer')) {
                $message .= sprintf(' (from "%s")', $referer);
            }

            throw new ResourceNotFoundException($message, 404);
        } catch (MethodNotAllowedException $e) {
            $message = sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getPathInfo(), implode(', ', $e->getAllowedMethods()));

            throw new MethodNotAllowedException($e->getAllowedMethods(), $message);
        }
    }

    /**
     * Build route collection
     *
     * @return RouteCollection
     */
    protected function getCollection()
    {
        return $this->collection
            ? $this->collection
            : $this->collection = $this->builder->build();
    }
}