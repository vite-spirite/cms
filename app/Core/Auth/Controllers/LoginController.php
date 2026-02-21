<?php

namespace App\Core\Auth\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class LoginController
{
    public function __invoke(): Response
    {
        return Inertia::render('Auth::Login');
    }
}
