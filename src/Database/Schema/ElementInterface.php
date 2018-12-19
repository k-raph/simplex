<?php
/**
 * Simplex Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Simplex\Database\Schema;

interface ElementInterface
{
    /**
     * Get element name (unquoted).
     *
     * @return string
     */
    public function getName(): string;
}