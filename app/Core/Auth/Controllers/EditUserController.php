<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class EditUserController
{
    public function __invoke(User $user): Response
    {

        return Inertia::render('Auth::EditUser', ['user' => $user]);
    }
}
