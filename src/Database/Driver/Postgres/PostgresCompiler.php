<?php
/**
 * Simplex Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Driver\Postgres;

use Simplex\Database\Driver\Compiler as AbstractCompiler;

/**
 * Postgres syntax specific compiler.
 */
class PostgresCompiler extends AbstractCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compileInsert(
        string $table,
        array $columns,
        array $rowsets,
        string $primaryKey = null
    ): string {
        return parent::compileInsert(
                $table,
                $columns,
                $rowsets
            ) . (!empty($primaryKey) ? ' RETURNING ' . $this->quote($primaryKey) : '');
    }

    /**
     * {@inheritdoc}
     */
    protected function compileDistinct($distinct): string
    {
        if (empty($distinct)) {
            return '';
        }

        return 'DISTINCT' . (is_string($distinct) ? '(' . $this->quote($distinct) . ')' : '');
    }
}
