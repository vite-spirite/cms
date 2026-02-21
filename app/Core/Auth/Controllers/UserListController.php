<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserListController
{
    public function __invoke(): Response
    {
        $users = User::all();
        return Inertia::render('Auth::ListUser', ['users' => $users]);
    }
}
