<?php

namespace App\Core\Permissions\Controllers;

use App\Core\Permissions\Models\Role;
use App\Core\Permissions\Service\PermissionRegistry;
use Inertia\Inertia;
use Inertia\Response;

class RoleUpdateController
{
    public function __invoke(Role $role): Response
    {
        $role->loadMissing('permissions', 'users')->first();

        $baseRole = collect([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->map(fn($permission) => $permission->name)->toArray(),
        ]);

        return Inertia::render('Permissions::RoleUpdate', [
            'role' => $baseRole,
            'availablePermissions' => fn() => app()->make(PermissionRegistry::class)->groupByModule(),
            'members' => $role->users
        ]);
    }
}
