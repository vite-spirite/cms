<?php

namespace App\Core\Module\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'module';

    protected array $permissions = [];

    protected array $navigations = [];
}
