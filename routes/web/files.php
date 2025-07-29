<?php

use App\Http\Controllers\File\FileController;

Route::middleware(['auth', 'verified'])
    ->prefix('files')
    ->name('files.')
    ->controller(FileController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::delete('{file}/force-delete', 'forceDelete')->middleware('role:admin|super-admin')->withTrashed()->name('force-delete');

        Route::get('recycle', 'recycle')->name('recycle');
        Route::post('{file}/restore', 'restore')->withTrashed()->name('restore');
        Route::delete('{file}', 'softDelete')->name('soft-delete');

        Route::put('{file}', 'update')->name('update');
        Route::post('{task}/new-file', 'store')->name('store');
    });
