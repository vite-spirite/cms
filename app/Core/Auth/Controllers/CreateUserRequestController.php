<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Events\UserCreated;
use App\Core\Auth\Models\User;
use App\Core\Auth\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Redirect;

class CreateUserRequestController
{
    public function __invoke(CreateUserRequest $request): \Illuminate\Http\RedirectResponse
    {

        if (!\Gate::allows('user_create')) {
            return Redirect::route('admin.home')->with(['error' => ['title' => 'Permissions', 'description' => 'Permissions denied !']]);
        }

        $payload = $request->validated();

        $user = new User();
        $user->name = $payload['name'];
        $user->email = $payload['email'];
        $user->password = $payload['password'];
        $user->save();

        UserCreated::dispatch($user, $payload['extensions']);
        return Redirect::route('admin.users.index')->with(['success' => ['title' => 'User', 'description' => 'Successfully Created User']]);
    }
}
