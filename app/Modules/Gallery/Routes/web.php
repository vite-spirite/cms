<?php


Route::get('/gallery/list', \App\Modules\Gallery\Controllers\GalleryListController::class)->name('gallery.list');
Route::post('/gallery/uploads', \App\Modules\Gallery\Controllers\GalleryUploadFilesController::class)->name('gallery.uploads')->middleware('can:gallery_upload');
Route::delete('/gallery/delete/{media}', \App\Modules\Gallery\Controllers\GalleryMediaDeleteController::class)->name('gallery.delete')->middleware('can:gallery_delete');
