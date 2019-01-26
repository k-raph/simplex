<?php

namespace Simplex\Database\Driver;

use Finesse\QueryScribe\GrammarInterface;
use PDO;
use PDOException;

class SqliteDriver extends AbstractDriver
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Sqlite driver implementation
     *
     * @param array $options
     * @param GrammarInterface $grammar
     */
    public function __construct(array $options, GrammarInterface $grammar)
    {
        $this->options = $options;
        $this->grammar = $grammar;

        try {
            $this->connect();
        } catch (PDOException $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        try {
            $path = $this->options['database'];
            $path = realpath('../'.$path);
            $dsn = "sqlite:$path";
            $this->pdo = new PDO($dsn);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
