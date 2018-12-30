<?php

namespace Simplex\Database;

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
     * @var string
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
     * @return DriverInterface
     */
    public function getDriver(): DriverInterface
    {
        $driver = $this->drivers[$this->default['type']] ?? null;
        if (!$driver) {
            throw new \UnexpectedValueException(sprintf('Provided databas type is incorrect or is not supported %s', (string)$driver));
        }

        return new $driver($this->default);
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
