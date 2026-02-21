<?php

namespace App\Core\Auth\Providers;

use App\Core\Auth\Models\User;
use App\Core\Module\BaseModuleServiceProvider;
use App\Core\Module\ModuleHelper;
use App\Core\Permissions\Events\RoleCreated;
use App\Core\Permissions\Events\RoleUpdated;
use Inertia\Inertia;

class AuthServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'auth';

    protected array $permissions = [
        'user_create' => [
            'name' => 'Create new user',
            'description' => 'Create new user',
        ],
        'user_edit' => [
            'name' => 'Edit user',
            'description' => 'Edit user',
        ],
        'user_delete' => [
            'name' => 'Delete user',
            'description' => 'Delete user',
        ],
        'user_view' => [
            'name' => 'View user',
            'description' => 'View user',
        ]
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'users',
                'icon' => 'i-lucide-user',
                'children' => [
                    ['label' => 'create user', 'icon' => 'i-lucide-user', 'route' => 'admin.users.create'],
                    ['label' => 'list users', 'icon' => 'i-lucide-user', 'route' => 'admin.users.index']
                ],
            ]
        ];
    }

    public function boot(): void
    {
        parent::boot();

        Inertia::share([
            'users' => Inertia::optional(fn() => User::all()),
        ]);

        ModuleHelper::when('Permissions', function () {
            \Event::listen(RoleCreated::class, function (RoleCreated $event) {
                if (auth()->user()->can('role_assign')) {
                    $event->role->users()->sync($event->payload['users']);
                }
            });

            \Event::listen(RoleUpdated::class, function (RoleUpdated $event) {
                if (auth()->user()->can('role_assign')) {
                    $event->role->users()->sync($event->payload['users']);
                }
            });
        });
    }

}
