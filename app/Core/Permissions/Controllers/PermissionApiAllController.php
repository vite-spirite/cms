<?php


namespace App\Core\Permissions\Controllers;

use App\Core\Permissions\Service\PermissionRegistry;
use Illuminate\Http\JsonResponse;

class PermissionApiAllController
{
    public function __invoke(): JsonResponse
    {
        $permissionRegistry = app()->make(PermissionRegistry::class);
        $permissions = $permissionRegistry->groupByModule();

        return response()->json($permissions);
    }
}
