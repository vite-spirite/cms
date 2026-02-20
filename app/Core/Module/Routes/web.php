<?php

Route::prefix('admin')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        return \Inertia\Inertia::render('Module::home');
    })->name('admin.home');
});
