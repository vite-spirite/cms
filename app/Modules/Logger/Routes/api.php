<?php

Route::get('/logger/since', \App\Modules\Logger\Controllers\LoggerGetSinceController::class)->name('logger.since')->middleware('can:logger_view');
