<?php

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return \Inertia\Inertia::render('Module::home');
    })->name('admin.home');
});
