<?php
/**
 * Simplex, Core Components
 *
 * @author Wolfy-J
 */

namespace Simplex\Database\Driver\MySQL\Schema;

use Simplex\Database\Schema\AbstractIndex;

class MySQLIndex extends AbstractIndex
{
    /**
     * @param string $table
     * @param string $name
     * @param array  $schema
     * @return MySQLIndex
     */
    public static function createInstance(string $table, string $name, array $schema): self
    {
        $index = new self($table, $name);

        foreach ($schema as $definition) {
            $index->type = $definition['Non_unique'] ? self::NORMAL : self::UNIQUE;
            $index->columns[] = $definition['Column_name'];
        }

        return $index;
    }
}