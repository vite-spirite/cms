<?php

namespace App\Core\Permissions\Providers;

use App\Core\Auth\Events\UserCreated;
use App\Core\Auth\Events\UserEdited;
use App\Core\Auth\Models\User;
use App\Core\Module\ModuleHelper;
use App\Core\Permissions\Models\Permission;
use App\Core\Permissions\Models\Role;
use App\Core\Permissions\Service\PermissionRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PermissionServiceProvider extends \App\Core\Module\BaseModuleServiceProvider
{
    protected string $name = 'permissions';

    protected array $permissions = [
        'role_create' => [
            'name' => 'Create new roles',
            'description' => 'Create new roles ability',
        ],
        'role_read' => [
            'name' => 'Read roles',
            'description' => 'Read roles ability',
        ],
        'role_update' => [
            'name' => 'Update roles',
            'description' => 'Update roles ability',
        ],
        'role_delete' => [
            'name' => 'Delete roles',
            'description' => 'Delete roles ability',
        ],
        'role_assign' => [
            'name' => 'Assign roles',
            'description' => 'Assign roles to users',
        ],
        'permission_assign' => [
            'name' => 'Assign permissions',
            'description' => 'Assign permissions to users',
        ]
    ];

    public function getNavigations(): array
    {
        return [
            [
                'label' => 'Permissions',
                'icon' => 'i-lucide-shield',
                'children' => [
                    [
                        'label' => 'List roles',
                        'icon' => 'i-lucide-shield-minus',
                        'route' => 'permissions.roles.list'

                    ],
                    [
                        'label' => 'Create roles',
                        'icon' => 'i-lucide-shield-plus',
                        'route' => 'permissions.roles.create'
                    ],
                ]
            ]
        ];
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(PermissionRegistry::class, function () {
            return new PermissionRegistry();
        });

    }

    public function boot(): void
    {
        parent::boot();

        $this->extendUserModel();
        $this->registerGates();
        $this->shareInertia();
        $this->listenUserEvents();


    }

    protected function extendUserModel(): void
    {
        if (!class_exists(User::class)) {
            return;
        }

        $user = new User();
        $user->mergeFillable(['is_owner']);
        $user->mergeCasts(['is_owner' => 'boolean']);

        User::macro('isOwner', function () {
            return $this->is_owner;
        });

        User::resolveRelationUsing('roles', function (User $userModel) {
            return $userModel->belongsToMany(Role::class, 'user_role');
        });

        User::resolveRelationUsing('permissions', function (User $userModel) {
            return $userModel->belongsToMany(Permission::class, 'user_permissions');
        });

        User::macro('syncRoles', function (array $roles) {
            $this->roles()->sync($roles);
        });

        User::macro('syncPermissions', function (array $permissions) {
            $this->permissions()->sync($permissions);
        });

        User::macro('getPermissions', function () {
            $rolePermissions = $this->roles()->with('permissions')->get()->pluck('permissions')->flatten();
            $directPermissions = $this->permissions()->get();

            return $rolePermissions->merge($directPermissions)->unique('id')->pluck('name');
        });

        User::macro('hasPermission', function (string $permission) {
            return $this->getPermissions()->contains($permission);
        });

        User::macro('hasAllPermissions', function (array $permissions) {
            $userPermissions = $this->getPermissions();
            return collect($permissions)->diff($userPermissions)->isEmpty();
        });
    }

    protected function registerGates(): void
    {
        $this->app->booted(function () {

            Gate::before(function ($user, $ability) {
                if (User::hasMacro('isOwner') && $user->isOwner()) {
                    return true;
                }
            });

            $permissionRegistry = $this->app->make(PermissionRegistry::class);
            foreach ($permissionRegistry->all() as $permission) {
                Gate::define($permission['name'], function ($user) use ($permission) {
                    return User::hasMacro('hasPermission') ? $user->hasPermission($permission['name']) : false;
                });
            }
        });
    }

    protected function shareInertia()
    {
        Inertia::share([
            'permissions' => [
                'capabilities' => fn() => Auth::check() ? Auth::user()->getPermissions() : [],
                'owner' => fn() => Auth::check() ? Auth::user()->isOwner() : false,
            ]
        ]);
    }

    private function listenUserEvents(): void
    {
        ModuleHelper::when('Auth', function () {
            \Event::listen(UserCreated::class, function (UserCreated $event) {
                $user = \auth()->user();
                if ($user->can('role_assign')) {
                    $event->user->roles()->sync($event->payload['roles']);
                }

                if ($user->can('permission_assign')) {
                    $this->syncDatabasePermissions();
                    $ids = Permission::whereIn('name', $event->payload['permissions'])->pluck('id')->all();
                    $event->user->permissions()->sync($ids);
                }
            });

            \Event::listen(UserEdited::class, function (UserEdited $event) {
                $user = \auth()->user();

                if ($user->can('role_assign')) {
                    $event->user->roles()->sync($event->payload['roles']);
                }

                if ($user->can('permission_assign')) {
                    $this->syncDatabasePermissions();
                    $ids = Permission::whereIn('name', $event->payload['permissions'])->pluck('id')->all();
                    $event->user->permissions()->sync($ids);
                }
            });
        });
    }

    private function syncDatabasePermissions(): void
    {
        $permissionRegistry = $this->app->make(PermissionRegistry::class);
        $permissionRegistry->sync();
    }
}
