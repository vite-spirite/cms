<?php


Route::prefix('admin/page')->group(function () {
    Route::get('/list', \App\Modules\PageBuilder\Controllers\PageBuilderListController::class)->name('page.list');
    Route::get('/create', \App\Modules\PageBuilder\Controllers\PageBuilderCreateController::class)->middleware('can:page_create')->name('page.create');
    Route::post('/create', \App\Modules\PageBuilder\Controllers\PageBuilderCreateRequestController::class)->middleware('can:page_create')->name('page.create.request');

    Route::get('/edit/{page}', \App\Modules\PageBuilder\Controllers\PageBuilderEditController::class)->middleware('can:page_edit')->name('page.edit');
    Route::put('/edit/{page}', \App\Modules\PageBuilder\Controllers\PageBuilderEditRequestController::class)->middleware('can:page_edit')->name('page.edit.request');

    Route::delete('/delete/{page}', \App\Modules\PageBuilder\Controllers\PageBuilderDeleteController::class)->middleware('can:page_delete')->name('page.delete');
});

Route::get('{slug?}', \App\Modules\PageBuilder\Controllers\PageBuilderRenderController::class)->name('page.render');
