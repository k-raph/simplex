<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 12:13
 */

namespace Simplex\Console\Command;


use Simplex\Routing\Route;
use Simplex\Routing\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RouterDebugCommand extends Command
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RouterDebugCommand constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        parent::__construct('router:debug');
        $this->router = $router;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $routes = $this->router->all();
        $style = new SymfonyStyle($input, $output);
        $routes = array_map(function (Route $route) {
            $handler = $route->getHandler();
            return [
                'name' => $route->getName(),
                'methods' => $route->getMethod(),
                'host' => $route->getHost(),
                'path' => $route->getPath(),
                'controller' => is_string($handler) ? $handler : \Closure::class
                //'middlewares' => join('|', $route->getMiddlewares())
            ];
        }, $routes);

        usort($routes, function (array $route1, array $route2) {
            return $route1['host'] > $route2['host'];
        });

        $style->table(['Name', 'Methods', 'Host', 'Path', 'Controller'], $routes);
    }

}