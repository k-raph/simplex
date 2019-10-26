<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 11:02
 */

namespace Simplex\Console;

use Simplex\Configuration\Configuration;
use Simplex\Kernel as BaseKernel;
use Simplex\Module\ModuleInterface;
use Simplex\Module\ModuleLoader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Kernel extends Application
{

    /**
     * @var BaseKernel
     */
    private $kernel;

    /**
     * @var bool
     */
    private $registeredCommand = false;

    /**
     * Kernel constructor.
     * @param BaseKernel $kernel
     */
    public function __construct(BaseKernel $kernel)
    {
        parent::__construct('Simplex Framework', '1.1');
        $this->kernel = $kernel;
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->registerCommands();
        return parent::run($input, $output);
    }

    /**
     * Register commands to the kernel
     */
    protected function registerCommands()
    {
        if ($this->registeredCommand) {
            return;
        }

        $container = $this->kernel->getContainer();

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);
        $configuration->set('providers', $configuration->get('console.providers', []), true);

        // Get base commands
        $commands = $configuration->get('console.commands', []);

        // Try to add commands provided by registered modules
        try {
            $this->kernel->boot();
            /** @var ModuleLoader $loader */
            $loader = $container->get(ModuleLoader::class);
            $commands = array_reduce($loader->getModules(), function (array $commands, ModuleInterface $module) {
                $commands = array_merge($commands, $module->getCommands());
                return $commands;
            }, $commands);
        } catch (\Exception $_) {
        }

        foreach ($commands as $command) {
            $this->add($container->get($command));
        }

        $this->registeredCommand = true;
    }
}
