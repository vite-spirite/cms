<?php

namespace App\Core\Auth\Controllers;

use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function __invoke()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return \Redirect::route('login');
    }
}
