<?php
/**
 * Simplex Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Driver\Traits;


use Simplex\Logger\Traits\LoggerTrait;

trait ProfilingTrait
{
    use LoggerTrait;

    /** @var bool */
    private $profiling = false;

    /**
     * Enable or disable driver query profiling.
     *
     * @param bool $profiling Enable or disable driver profiling.
     */
    public function setProfiling(bool $profiling = true)
    {
        $this->profiling = $profiling;
    }

    /**
     * Check if profiling mode is enabled.
     *
     * @return bool
     */
    public function isProfiling(): bool
    {
        return $this->profiling;
    }
}