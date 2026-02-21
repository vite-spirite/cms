<?php


namespace App\Core\Permissions\Controllers;

use App\Core\Auth\Models\User;
use Illuminate\Http\JsonResponse;

class PermissionApiGetController
{
    public function __invoke(User $user): JsonResponse
    {
        $permissions = $user->permissions()->get();
        return response()->json($permissions);
    }
}
