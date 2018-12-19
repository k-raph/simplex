<?php

namespace Simplex\Database;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Simplex\Database\Driver\SQLite\SQLiteDriver;
use Simplex\Database\Driver\MySQL\MySQLDriver;
use Simplex\Database\Config\DatabaseConfig;

class DatabaseServiceProvider extends AbstractServiceProvider
{

    /**
     * {@inheritDoc}
     */
    protected $provides = [
        DatabaseInterface::class,
        DatabaseManager::class
    ];

    /**
     * Drivers map
     *
     * @var array
     */
    private $drivers = [
        'sqlite' => SQLiteDriver::class,
        'mysql' => MySQLDriver::class
    ];

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $config = $this->container->get('config')['database'] ?? [];
        $config['connections'] = array_map(function ($connection) {
            $type = $connection['type'];
            if ('sqlite' === $type) {
                $connection['connection'] = 'sqlite:'.realpath(dirname(__DIR__).'/../'.$connection['path']);
                unset($connection['path']);
            }
            $connection['driver'] = $this->drivers[$type];
            return $connection;
        }, $this->getConfig($config)['connections']);
        
        $manager = new DatabaseManager(new DatabaseConfig($config));

        $this->container->add(DatabaseManager::class, $manager);
        $this->container->add(DatabaseInterface::class, $manager->database());
    }

    /**
     * Get formatted config
     *
     * @param array $config
     * @return array
     */
    private function getConfig(array $config): array
    {
        return array_merge([
            'default'     => 'default',
            'databases'   => [],
            'connections' => []
           ], $config);
    }

}