<?php

namespace Simplex\Database\Driver;

use Finesse\QueryScribe\GrammarInterface;
use PDO;

abstract class AbstractDriver implements DriverInterface
{

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * {@inheritdoc}
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @return GrammarInterface
     */
    public function getGrammar(): GrammarInterface
    {
        return $this->grammar;
    }
}
