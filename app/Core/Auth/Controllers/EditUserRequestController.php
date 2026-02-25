<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Events\UserEdited;
use App\Core\Auth\Models\User;
use App\Core\Auth\Requests\EditUserRequest;
use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;

class EditUserRequestController
{
    public function __invoke(EditUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $payload = $request->validated();

        $user = User::where('id', $payload['id'])->first();

        if (!$user) {
            return \Redirect::back()->with(['error' => ['title' => 'Edit user', 'description' => 'User not found']]);
        }

        $original = $user->toArray();

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

        ModuleHelper::when('Logger', function () use ($user, $original) {
            CmsLog::info('Auth', 'users.updated', "User '{$user->name}' successfully updated.", ['before' => $original, 'after' => $user->toArray()], $user);
        });

        return \Redirect::route('admin.users.index')->with(['success' => ['title' => 'User', 'description' => 'Successfully Updated User']]);
    }
}
