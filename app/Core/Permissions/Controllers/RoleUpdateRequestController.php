<?php

namespace App\Core\Permissions\Controllers;

use App\Core\Module\ModuleHelper;
use App\Core\Permissions\Events\RoleUpdated;
use App\Core\Permissions\Models\Permission;
use App\Core\Permissions\Models\Role;
use App\Core\Permissions\Requests\UpdateRoleRequest;
use App\Core\Permissions\Service\PermissionRegistry;
use App\Modules\Logger\Facades\CmsLog;

class RoleUpdateRequestController
{
    public function __invoke(UpdateRoleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $payload = $request->validated();

        $role = Role::where('id', $payload['id'])->first();

        if (!$role) {
            return \Redirect::route('permissions.roles.list')->with('error', ['title' => 'Role update', 'description' => 'Database role not found.']);
        }

        $original = $role->toArray();

        $permissionRegistry = app()->make(PermissionRegistry::class);
        $permissionRegistry->sync();

        $role->name = $payload['name'];
        $permissions = Permission::select('id')->whereIn('name', $payload['permissions'])->pluck('id')->all();
        $role->permissions()->sync($permissions);
        $role->save();

        RoleUpdated::dispatch($role, $payload['extensions']);

        ModuleHelper::when('Logger', function () use ($role, $original) {
            CmsLog::info(category: 'Permission', action: 'role.updated', message: "Role '{$role->name}' successfully updated.", context: ['after' => $role->toArray(), 'before' => $original], subject: $role);
        });

        return \Redirect::route('permissions.roles.list')->with('success', ['title' => 'Role update', 'description' => 'Role updated successfully.']);
    }
}
