<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 12:10
 */

namespace Simplex\Console\Command;

use Symfony\Component\Yaml\Command\LintCommand;

class YamlLintCommand extends LintCommand
{

    /**
     * YamlLintCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('lint:yaml');
    }
}
