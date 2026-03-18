<?php


Route::get('/users', \App\Core\Auth\Controllers\ApiUsersController::class)->name('users.list');
