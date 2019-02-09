<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:32
 */

namespace Simplex\Module;

use Simplex\Configuration\Configuration;

abstract class AbstractModule implements ModuleInterface
{

    /**
     * @param Configuration $configuration
     * @return mixed|void
     */
    public function configure(Configuration $configuration)
    {
        // TODO: Implement loadConfig() method.
    }

    /**
     * @return array
     */
    public function getMappings(): array
    {
        return [];
    }
}