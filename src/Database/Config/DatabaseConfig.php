<?php
/**
 * Simplex Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Config;

use Simplex\Database\Exception\ConfigException;

class DatabaseConfig
{
    /**
     * @invisible
     * @var array
     */
    protected $config = [
        'default'     => 'default',
        'aliases'     => [],
        'databases'   => [],
        'connections' => [],
    ];

    /**
     * At this moment on array based configs can be supported.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new ConfigException("Undefined configuration key '{$offset}'");
        }

        return $this->config[$offset];
    }

    /**
     *{@inheritdoc}
     *
     * @throws ConfigException
     */
    public function offsetSet($offset, $value)
    {
        throw new ConfigException(
            'Unable to change configuration data, configs are treated as immutable by default'
        );
    }

    /**
     *{@inheritdoc}
     *
     * @throws ConfigException
     */
    public function offsetUnset($offset)
    {
        throw new ConfigException(
            'Unable to change configuration data, configs are treated as immutable by default'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->config);
    }

    /**
     * Restoring state.
     *
     * @param array $an_array
     *
     * @return static
     */
    public static function __set_state($an_array)
    {
        return new static($an_array['config']);
    }

    /**
     * @return string
     */
    public function getDefaultDatabase(): string
    {
        return $this->config['default'];
    }

    /**
     * Get named list of all databases.
     *
     * @return DatabasePartial[]
     */
    public function getDatabases(): array
    {
        $result = [];
        foreach (array_keys($this->config['databases']) as $database) {
            $result[$database] = $this->getDatabase($database);
        }

        return $result;
    }

    /**
     * Get names list of all driver connections.
     *
     * @return Autowire[]
     */
    public function getDrivers(): array
    {
        $result = [];
        foreach (array_keys($this->config['connections'] ?? $this->config['drivers']) as $driver) {
            $result[$driver] = $this->getDriver($driver);
        }

        return $result;
    }

    /**
     * @param string $database
     * @return bool
     */
    public function hasDatabase(string $database): bool
    {
        return isset($this->config['databases'][$database]);
    }

    /**
     * @param string $database
     * @return DatabasePartial
     *
     * @throws ConfigException
     */
    public function getDatabase(string $database): DatabasePartial
    {
        if (!$this->hasDatabase($database)) {
            throw new ConfigException("Undefined database `{$database}`.");
        }

        $config = $this->config['databases'][$database];

        return new DatabasePartial(
            $database,
            $config['tablePrefix'] ?? $config['prefix'] ?? '',
            $config['connection'] ?? $config['write'] ?? $config['driver'],
            $config['readConnection'] ?? $config['read'] ?? $config['readDriver'] ?? null
        );
    }

    /**
     * @param string $driver
     * @return bool
     */
    public function hasDriver(string $driver): bool
    {
        return isset($this->config['connections'][$driver]) || isset($this->config['drivers'][$driver]);
    }

    /**
     * @param string $driver
     * @return Autowire
     *
     * @throws ConfigException
     */
    public function getDriver(string $driver)
    {
        if (!$this->hasDriver($driver)) {
            throw new ConfigException("Undefined driver `{$driver}`.");
        }

        $config = $this->config['connections'][$driver] ?? $this->config['drivers'][$driver];

        $driver = $config['driver'] ?? $config['class'];

        return new $driver($config);
    }

    public function resolveAlias(string $alias): string
    {
        while (is_string($alias) && isset($this->config) && isset($this->config['aliases'][$alias])) {
            $alias = $this->config['aliases'][$alias];
        }

        return $alias;
    }
}