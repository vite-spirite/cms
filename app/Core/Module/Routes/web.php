<?php

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', \App\Core\Module\Controllers\ModuleListController::class)->name('admin.home');
    Route::get('/module/toggle', \App\Core\Module\Controllers\ModuleToggleController::class)->name('admin.module.toggle')->middleware('can:module_manage');
});
