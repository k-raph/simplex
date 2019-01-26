<?php

namespace Simplex\Database\Driver;

use Finesse\QueryScribe\GrammarInterface;
use PDO;

interface DriverInterface
{
    /**
     * Connects to database
     *
     * @return mixed
     */
    public function connect();

    /**
     * Gets the PDO instance
     *
     * @return PDO|null
     */
    public function getPdo(): ?PDO;

    /**
     * Retrieves associed grammar
     *
     * @return GrammarInterface
     */
    public function getGrammar(): GrammarInterface;
}
