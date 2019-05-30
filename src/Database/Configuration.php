<?php

namespace Simplex\Database;

use Finesse\QueryScribe\Grammars\CommonGrammar;
use Finesse\QueryScribe\Grammars\MySQLGrammar;
use Finesse\QueryScribe\Grammars\SQLiteGrammar;
use Simplex\Database\Driver\DriverInterface;
use Simplex\Database\Driver\SqliteDriver;

class Configuration
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Default connection
     *
     * @var array
     */
    protected $default;

    /**
     * @var array
     */
    protected $drivers = [
        'sqlite' => SqliteDriver::class
    ];

    public function __construct(array $options)
    {
        $this->options = $options;
        $this->default = $this->options['connections'][$this->options['default']];
    }

    /**
     * Get used driver
     *
     * @param string $name
     * @return DriverInterface
     */
    public function getDriver(?string $name = null): DriverInterface
    {
        $options = $this->options['connections'][$name ?? $this->options['default']];
        $driver = $this->drivers[$options['type']] ?? null;
        if (!$driver) {
            throw new \UnexpectedValueException(sprintf('Provided database type is incorrect or is not supported %s', (string)$driver));
        }

        switch (strtolower($options['type'] ?? '')) {
            case 'mysql':
                $grammar = new MySQLGrammar();
                break;
            case 'sqlite':
                $grammar = new SQLiteGrammar();
                break;
            default:
                $grammar = new CommonGrammar();
        }

        return new $driver($options, $grammar);
    }

    /**
     * Get PDO specific options or attributes
     *
     * @return array
     */
    public function getPdoOptions(): array
    {
        return $this->default['pdo_options'] ?? [];
    }
}
