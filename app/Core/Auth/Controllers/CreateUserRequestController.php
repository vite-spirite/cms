<?php

namespace App\Core\Auth\Controllers;

use App\Core\Auth\Events\UserCreated;
use App\Core\Auth\Models\User;
use App\Core\Auth\Requests\CreateUserRequest;
use App\Core\Module\ModuleHelper;
use App\Modules\Logger\Facades\CmsLog;
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

        ModuleHelper::when('Logger', function () use ($user) {
            CmsLog::info('Auth', 'user.created', "User '{$user->name}' successfully created.", ['user' => $user->toArray()], $user);
        });

        return Redirect::route('admin.users.index')->with(['success' => ['title' => 'User', 'description' => 'Successfully Created User']]);
    }
}
