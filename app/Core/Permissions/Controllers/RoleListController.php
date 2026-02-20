<?php


namespace App\Core\Permissions\Controllers;

use App\Core\Permissions\Models\Role;
use Inertia\Inertia;
use Inertia\Response;

class RoleListController
{
    public function __invoke(): Response
    {
        $roles = Role::orderBy('id', 'desc')->withCount(['permissions', 'users'])->get();

        return Inertia::render('Permissions::RoleList', [
            'roles' => $roles,
            'has_role_create' => \Gate::allows('role_create'),
            'has_role_delete' => \Gate::allows('role_delete'),
            'has_role_edit' => \Gate::allows('role_update'),
        ]);
    }
}
