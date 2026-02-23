<?php

namespace App\Core\Module\Providers;

use App\Core\Module\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'module';

    protected array $permissions = [
        'module_manage' => [
            'name' => 'Manage modules',
            'description' => 'Load or unload modules',
        ]
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'Modules',
                'route' => 'admin.home',
                'icon' => 'i-lucide-blocks',
            ]
        ];
    }
}
