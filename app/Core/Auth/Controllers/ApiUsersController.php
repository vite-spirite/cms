<?php

namespace App\Core\Auth\Controllers;

use Illuminate\Http\JsonResponse;

class ApiUsersController
{
    public function __invoke(): JsonResponse
    {
        $user = \App\Core\Auth\Models\User::orderBy('created_at', 'desc')->get();

        return \Response::json($user);
    }
}
