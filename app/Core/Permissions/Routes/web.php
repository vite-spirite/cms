<?php

Route::prefix('admin/permissions')->middleware(['web', 'auth'])->group(function () {
    Route::get('/roles/list', \App\Core\Permissions\Controllers\RoleListController::class)->middleware('can:role_read')->name('permissions.roles.list');
    Route::get('/roles/create', \App\Core\Permissions\Controllers\RoleCreateController::class)->middleware('can:role_create')->name('permissions.roles.create');
    Route::post('/roles/create', \App\Core\Permissions\Controllers\RoleCreateRequestController::class)->middleware('can:role_create')->name('permissions.roles.create.request');

    Route::delete('/roles/delete/{role}', \App\Core\Permissions\Controllers\RoleDeleteController::class)->name('permissions.roles.delete');

    Route::get('/roles/edit/{role}', \App\Core\Permissions\Controllers\RoleUpdateController::class)->middleware('can:role_update')->name('permissions.roles.edit');
    Route::put('/role/edit/{role}', \App\Core\Permissions\Controllers\RoleUpdateRequestController::class)->middleware('can:role_update')->name('permissions.roles.edit.request');
});
