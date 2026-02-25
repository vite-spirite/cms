<?php

namespace App\Core\Permissions\Controllers;

use App\Core\Module\ModuleHelper;
use App\Core\Permissions\Events\RoleCreated;
use App\Core\Permissions\Models\Permission;
use App\Core\Permissions\Models\Role;
use App\Core\Permissions\Requests\CreateRoleRequest;
use App\Core\Permissions\Service\PermissionRegistry;
use App\Modules\Logger\Facades\CmsLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class RoleCreateRequestController
{
    public function __invoke(CreateRoleRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $permissionRegistry = app()->make(PermissionRegistry::class);
        $permissionRegistry->sync();

        $role = new Role();
        $role->name = $payload['name'];

        $role->save();

        $permissions = Permission::whereIn('name', $payload['permissions'])->pluck('id')->all();
        $role->permissions()->sync($permissions);

        RoleCreated::dispatch($role, $payload['extensions']);

        ModuleHelper::when('Logger', function () use ($role) {
            CmsLog::info(category: 'Permission', action: 'role.created', message: "Role '{$role->name}' successfully created.", context: $role->toArray(), subject: $role);
        });

        return Redirect::route('permissions.roles.list')->with(['success' => ['title' => 'Role', 'description' => 'Role created']]);
    }
}
