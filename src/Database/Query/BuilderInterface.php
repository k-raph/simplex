<?php
/**
 * Simplex Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Query;

use Simplex\Database\Exception\BuilderException;
use Simplex\Database\Injection\ExpressionInterface;
use Simplex\Database\Injection\ParameterInterface;

interface BuilderInterface extends ExpressionInterface
{
    /**
     * Get ordered list of builder parameters in a form of ParameterInterface array.
     *
     * @return ParameterInterface[]
     * @throws BuilderException
     */
    public function getParameters(): array;
}