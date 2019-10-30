<?php

namespace App\AskeetModule;

use Simplex\Module\AbstractModule;

class AskeetModuleProvider extends AbstractModule
{

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'askeet';
    }

    /**
     * @return string|null
     */
    public function getMigrationsConfig(): ?string
    {
        return __DIR__ . '/resources/migrations.yml';
    }
}