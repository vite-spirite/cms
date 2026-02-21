<?php


namespace App\Core\Auth\Controllers;

use App\Core\Auth\Models\User;

class DeleteUserController
{
    public function __invoke(User $user)
    {
        $user->delete();

        return \Redirect::back()->with(['success' => ['title' => 'User deleted', 'description' => 'The user has been deleted.']]);
    }
}
