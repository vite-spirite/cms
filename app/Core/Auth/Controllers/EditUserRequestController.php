<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Events\UserEdited;
use App\Core\Auth\Models\User;
use App\Core\Auth\Requests\EditUserRequest;

class EditUserRequestController
{
    public function __invoke(EditUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $payload = $request->validated();

        $user = User::where('id', $payload['id'])->first();

        if (!$user) {
            return \Redirect::back()->with(['error' => ['title' => 'Edit user', 'description' => 'User not found']]);
        }

        if ($payload['email'] != $user->email) {
            $otherUser = User::where('email', $payload['email'])->whereNot('id', $user->id)->first();

            if ($otherUser) {
                return \Redirect::back()->with(['error' => ['title' => 'Edit user', 'description' => 'User with this email already exists']]);
            }

            $user->email = $payload['email'];
        }

        if ($payload['password']) {
            $user->password = $payload['password'];
        }

        if ($payload['name']) {
            $user->name = $payload['name'];
        }

        $user->save();

        UserEdited::dispatch($user, $payload['extensions']);

        return \Redirect::route('admin.users.index')->with(['success' => ['title' => 'User', 'description' => 'Successfully Updated User']]);
    }
}
