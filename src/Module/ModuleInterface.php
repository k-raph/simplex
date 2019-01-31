<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/01/2019
 * Time: 18:07
 */

namespace Simplex\Module;


use Simplex\Configuration\Configuration;

interface ModuleInterface
{
    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Register configuration
     *
     * @param Configuration $configuration
     * @return mixed
     */
    public function loadConfig(Configuration $configuration);
}