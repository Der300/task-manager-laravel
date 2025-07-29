<?php

use App\Http\Controllers\File\FileController;

Route::middleware(['auth', 'verified'])
    ->prefix('myfiles')
    ->name('myfiles.')
    ->controller(FileController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('recycle', 'recycle')->name('recycle');

        Route::delete('{file}/force-delete', 'forceDelete')->middleware('role:admin|super-admin', 'password.confirm')->withTrashed()->name('force-delete');

        Route::post('{file}/restore', 'restore')->withTrashed()->name('restore');

        Route::get('{file}/download', 'download')->name('download');
        Route::post('{task}/upload', 'upload')->name('upload');

        Route::delete('{file}', 'softDelete')->name('soft-delete');
    });
