<?php

namespace Simplex\Database;

class Connection
{
    
    /**
     * Pdo instance
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Constructor
     *
     * @param string $type
     * @param array $config
     */
    public function __construct($type, array $config)
    {
        if ($type == 'sqlite') {
            $db = $config['name'];
            try {
                $path = dirname(__DIR__).'/../resources';
                $pdo = new \PDO("sqlite:$path/$db");
                $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->pdo = $pdo;
            } catch (\PDOException $e) {
                die($e->getMessage());        
            }
        }

    }

    /**
     * Get current pdo instance
     *
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }
}