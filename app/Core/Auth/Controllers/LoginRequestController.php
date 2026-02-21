<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Requests\LoginRequest;

class LoginRequestController
{
    public function __invoke(LoginRequest $request)
    {
        $payload = $request->validated();
        if (\Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']], $payload['remember'])) {
            return \Redirect::route('admin.home');
        }

        return \Redirect::back()->withErrors(['login' => 'invalid credentials']);
    }
}
