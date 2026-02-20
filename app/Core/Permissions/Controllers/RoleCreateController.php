<?php


namespace App\Core\Permissions\Controllers;

use App\Core\Permissions\Service\PermissionRegistry;
use Inertia\Inertia;
use Inertia\Response;

class RoleCreateController
{
    public function __invoke(): Response
    {
        return Inertia::render('Permissions::RoleCreate', [
            'availablePermissions' => fn() => app()->make(PermissionRegistry::class)->groupByModule()
        ]);
    }
}
