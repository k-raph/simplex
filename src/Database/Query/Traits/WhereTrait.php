<?php
/**
 * Simplex Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Query\Traits;

use Simplex\Database\Exception\BuilderException;
use Simplex\Database\Injection\ExpressionInterface;
use Simplex\Database\Injection\FragmentInterface;
use Simplex\Database\Injection\Parameter;
use Simplex\Database\Injection\ParameterInterface;
use Simplex\Database\Query\BuilderInterface;

trait WhereTrait
{
    /**
     * Set of generated where tokens, format must be supported by QueryCompilers.
     *
     * @var array
     */
    protected $whereTokens = [];

    /**
     * Parameters collected while generating WHERE tokens, must be in a same order as parameters
     * in resulted query.
     *
     * @var array
     */
    protected $whereParameters = [];

    /**
     * Simple WHERE condition with various set of arguments.
     *
     * @see AbstractWhere
     *
     * @param mixed ...$args [(column, value), (column, operator, value)]
     *
     * @return self|$this
     *
     * @throws BuilderException
     */
    public function where(...$args): self
    {
        $this->createToken('AND', $args, $this->whereTokens, $this->whereWrapper());

        return $this;
    }

    /**
     * Simple AND WHERE condition with various set of arguments.
     *
     * @see AbstractWhere
     *
     * @param mixed ...$args [(column, value), (column, operator, value)]
     *
     * @return self|$this
     *
     * @throws BuilderException
     */
    public function andWhere(...$args): self
    {
        $this->createToken('AND', $args, $this->whereTokens, $this->whereWrapper());

        return $this;
    }

    /**
     * Simple OR WHERE condition with various set of arguments.
     *
     * @see AbstractWhere
     *
     * @param mixed ...$args [(column, value), (column, operator, value)]
     *
     * @return self|$this
     *
     * @throws BuilderException
     */
    public function orWhere(...$args): self
    {
        $this->createToken('OR', $args, $this->whereTokens, $this->whereWrapper());

        return $this;
    }

    /**
     * Convert various amount of where function arguments into valid where token.
     *
     * @see AbstractWhere
     *
     * @param string   $joiner     Boolean joiner (AND | OR).
     * @param array    $parameters Set of parameters collected from where functions.
     * @param array    $tokens     Array to aggregate compiled tokens. Reference.
     * @param callable $wrapper    Callback or closure used to wrap/collect every potential
     *                             parameter.
     *
     * @throws BuilderException
     */
    abstract protected function createToken(
        $joiner,
        array $parameters,
        &$tokens = [],
        callable $wrapper
    );

    /**
     * Applied to every potential parameter while where tokens generation. Used to prepare and
     * collect where parameters.
     *
     * @return \Closure
     */
    private function whereWrapper()
    {
        return function ($parameter) {
            if ($parameter instanceof FragmentInterface) {
                //We are only not creating bindings for plan fragments
                if (!$parameter instanceof ParameterInterface && !$parameter instanceof BuilderInterface) {
                    return $parameter;
                }
            }

            if (is_array($parameter)) {
                throw new BuilderException('Arrays must be wrapped with Parameter instance');
            }

            //Wrapping all values with ParameterInterface
            if (!$parameter instanceof ParameterInterface && !$parameter instanceof ExpressionInterface) {
                $parameter = new Parameter($parameter, Parameter::DETECT_TYPE);
            };

            //Let's store to sent to driver when needed
            $this->whereParameters[] = $parameter;

            return $parameter;
        };
    }

    /**
     * Fill where tokens and parameters
     *
     * @param array $tokens
     * @param array $parameters
     * @return self
     */
    public function fillWhere(array $tokens, array $parameters)
    {
        $this->whereTokens = array_merge($this->whereTokens, $tokens);
        $this->whereParameters = array_merge($this->whereParameters, $parameters);

        return $this;
    }
}