<?php


namespace App\Core\Permissions\Controllers;

use App\Core\Permissions\Models\Role;

class RoleApiAllController
{
    public function __invoke()
    {
        $roles = Role::all();

        return response()->json($roles);
    }
}
