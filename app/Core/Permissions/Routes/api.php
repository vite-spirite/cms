<?php


Route::get('/roles/all', \App\Core\Permissions\Controllers\RoleApiAllController::class)->name('roles.all')->middleware('can:role_read');
Route::get('/permissions/all', \App\Core\Permissions\Controllers\PermissionApiAllController::class)->name('permissions.all')->middleware('can:role_read');

Route::get('/permissions/get/{user}', \App\Core\Permissions\Controllers\PermissionApiGetController::class)->name('permissions.get')->middleware('can:role_read');
Route::get('/roles/get/{user}', \App\Core\Permissions\Controllers\RoleApiGetController::class)->name('roles.get')->middleware('can:role_read');
