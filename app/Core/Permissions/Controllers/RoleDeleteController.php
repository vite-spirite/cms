<?php


namespace App\Core\Permissions\Controllers;

use App\Core\Permissions\Models\Role;

class RoleDeleteController
{
    public function __invoke(Role $role)
    {

        if (!\Gate::allows('role_delete')) {
            return \Redirect::back()->with(['error' => ['title' => 'Role deleting', 'description' => 'This action is unauthorized.']]);
        }

        $roleName = $role->name;
        $role->delete();

        return \Redirect::back()->with(['success' => ['title' => 'Role deleted', 'description' => "Role {$roleName} has been deleted"]]);
    }
}
