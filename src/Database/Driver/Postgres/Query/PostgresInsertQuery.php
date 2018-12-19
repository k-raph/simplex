<?php
/**
 * Simplex Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Driver\Postgres\Query;

use Simplex\Database\Driver\CompilerInterface;
use Simplex\Database\Driver\Postgres\PostgresCompiler;
use Simplex\Database\Driver\Postgres\PostgresDriver;
use Simplex\Database\Exception\BuilderException;
use Simplex\Database\Query\InsertQuery;

/**
 * Postgres driver requires little bit different way to handle last insert id.
 */
class PostgresInsertQuery extends InsertQuery
{
    /**
     * {@inheritdoc}
     *
     * @throws BuilderException
     */
    public function sqlStatement(CompilerInterface $compiler = null): string
    {
        if (
            !$this->driver instanceof PostgresDriver
            || (!empty($compiler) && !$compiler instanceof PostgresCompiler)
        ) {
            throw new BuilderException(
                'Postgres InsertQuery can be used only with Postgres driver and compiler'
            );
        }

        if (empty($compiler)) {
            $compiler = clone $this->compiler;
        }

        /**
         * @var PostgresDriver   $driver
         * @var PostgresCompiler $compiler
         */
        return $compiler->compileInsert(
            $this->table,
            $this->columns,
            $this->rowsets,
            $this->driver->getPrimary($this->compiler->getPrefix(), $this->table)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return (int)$this->driver->query($this->sqlStatement(),
            $this->getParameters())->fetchColumn();
    }
}
