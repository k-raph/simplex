<?php

namespace Simplex\Database\Driver;

use PDO;

interface DriverInterface
{
    public function connect();

    public function getPdo(): ?PDO;
}
