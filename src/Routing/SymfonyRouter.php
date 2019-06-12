<?php

namespace Simplex\Routing;

use Simplex\Http\MiddlewareInterface;
use Simplex\Routing\Middleware\StrategyInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouteCollectionBuilder;

class SymfonyRouter implements RouterInterface
{

    use RouteBuilderTrait;

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
     * @var MiddlewareInterface[]
     */
    private $middlewares = [
        'groups' => [],
        'routes' => []
    ];

    /**
     * @var string
     */
    private $host;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Constructor
     *
     * @param LoaderInterface $loader
     * @param string $host
     */
    public function __construct(LoaderInterface $loader, string $host)
    {
        $this->host = $host;
        $this->builder =  new RouteCollectionBuilder($loader);
        $this->builder->setHost($host);
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function import(string $from, array $options = [])
    {
        $options = array_merge([
            'prefix' => '/',
            'format' => 'yaml',
            'host' => $this->host
        ], $options);

        $builder = $this->builder->import($from, $options['prefix'], $options['format']);
        $builder = $builder->setHost($options['host']);
        unset($options['prefix'], $options['format'], $options['host']);

        foreach ($options as $key => $value) {
            $builder->setDefault("$key", $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function match(string $methods, string $path, $controller, ?string $name = null)
    {
        $this->builder
            ->add($path, $controller, $name)
            ->setMethods(explode('|', $methods));
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $name, array $parameters = []): string
    {
        return (new UrlGenerator($this->getCollection(), $this->context ?? new RequestContext()))->generate($name, $parameters, UrlGenerator::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Request $request): Route
    {
        try {
            $collection = $this->getCollection();
            $this->context = $context = (new RequestContext())->fromRequest($request);

            $matcher = new class($collection, $context) extends RedirectableUrlMatcher
            {
                /**
                 * {@inheritdoc}
                 */
                public function redirect($path, $route, $scheme = null)
                {
                    $match = $this->match($path);
                    return [
                        '_controller' => $match['_controller'],
                        'path' => $path,
                        'permanent' => true,
                        'scheme' => $scheme,
                        'httpPort' => $this->context->getHttpPort(),
                        'httpsPort' => $this->context->getHttpsPort(),
                        '_route' => $route,
                        '_strategy' => $match['_strategy'] ?? 'web'
                    ];
                }
            };

            $parameters = $matcher->matchRequest($request);

            $route = new Route($parameters['_route'], $parameters['_controller']);

            $route->setMiddlewares($parameters['_middlewares'] ?? []);
            $route->setStrategy($this->getStrategy($parameters['_strategy'] ?? 'web'));

            $parameters = array_filter($parameters, function (string $key) {
                return strpos($key, '_') !== 0;
            }, ARRAY_FILTER_USE_KEY);

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
        if ($this->collection) {
            return $this->collection;
        }

        $this->builder->setDefault('_middlewares', $this->middlewares['routes']);
        return $this->collection = $this->builder->build();
    }

    /**
     * {@inheritDoc}
     */
    public function middleware(MiddlewareInterface $middleware)
    {
        $this->middlewares['routes'] = $middleware;
    }

    /**
     * Get middleware stack associated to current middleware group
     *
     * @param string $name
     * @return StrategyInterface
     */
    private function getStrategy(string $name): StrategyInterface
    {
        $resolver = $this->middlewares['groups'][$name] ?? function () {
                return null;
            };
        $strategy = $resolver();

        if (is_null($strategy)) {
            throw new \LogicException(sprintf('Unregistered strategy: "%s"', $name));
        }

        return $strategy;
    }

    /**
     * {@inheritDoc}
     */
    public function setStrategy(string $strategy)
    {
        $this->builder->setDefault('_strategy', $strategy);
    }

    /**
     * @param string $name
     * @param callable $resolver
     */
    public function addStrategy(string $name, callable $resolver)
    {
        $this->middlewares['groups'][$name] = $resolver;
    }

    /**
     * Creates a new route collection builder
     *
     * @return RouteCollectionBuilder
     */
    public function newBuilder(): RouteCollectionBuilder
    {
        return new RouteCollectionBuilder($this->loader);
    }

    /**
     * @param string $prefix
     * @param RouteCollectionBuilder $builder
     */
    public function mount(string $prefix, RouteCollectionBuilder $builder)
    {
        $this->builder->mount($prefix, $builder);
    }
}