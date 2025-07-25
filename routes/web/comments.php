<?php

use App\Http\Controllers\Comment\CommentController;

Route::middleware(['auth', 'verified'])
    ->prefix('comments')
    ->name('comments.')
    ->controller(CommentController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{comment}/edit', 'edit')->name('edit');
        Route::put('{comment}', 'update')->name('update');

        Route::middleware('role:admin|super-admin')->group(function () {
            Route::get('recycle', 'recycle')->name('recycle');
            Route::delete('{comment}', 'softDelete')->name('soft-delete');
            Route::post('{comment}/restore', 'restore')->name('restore');
            Route::delete('{comment}/force-delete', 'forceDelete')->name('force-delete');
        });
    });
