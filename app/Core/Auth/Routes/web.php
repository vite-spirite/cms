<?php

Route::prefix('admin')->group(function () {
    Route::get('login', \App\Core\Auth\Controllers\LoginController::class)->name('login')->middleware('guest');
    Route::post('login', \App\Core\Auth\Controllers\LoginRequestController::class)->name('admin.login.request')->middleware(['guest', 'throttle:5,1']);

    Route::post('logout', \App\Core\Auth\Controllers\LogoutController::class)->name('admin.logout')->middleware('auth');

    Route::prefix('users')->middleware('auth')->group(function () {
        Route::get('/create', \App\Core\Auth\Controllers\CreateUserController::class)->name('admin.users.create')->middleware('can:user_create');
        Route::post('/create', \App\Core\Auth\Controllers\CreateUserRequestController::class)->name('admin.users.create.request')->middleware('can:user_create');
        Route::get('/list', \App\Core\Auth\Controllers\UserListController::class)->name('admin.users.index');

        Route::get('/edit/{user}', \App\Core\Auth\Controllers\EditUserController::class)->name('admin.users.edit')->middleware('can:user_edit');
        Route::put('/edit', \App\Core\Auth\Controllers\EditUserRequestController::class)->name('admin.users.edit.request')->middleware('can:user_edit');

        Route::delete('/delete/{user}', \App\Core\Auth\Controllers\DeleteUserController::class)->name('admin.users.delete')->middleware('can:user_delete');
    });
});
