<?php

namespace App\Core\Permissions\Controllers;

use App\Core\Auth\Models\User;
use Illuminate\Http\JsonResponse;

class RoleApiGetController
{
    public function __invoke(User $user): JsonResponse
    {
        $roles = $user->roles()->get();
        return response()->json($roles);
    }
}
