<?php

use App\Http\Controllers\Comment\CommentController;

Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->middleware(['auth', 'verified'])->name('comments.store');

Route::middleware(['auth', 'verified'])
    ->prefix('comments')
    ->name('comments.')
    ->controller(CommentController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::middleware('role:admin|super-admin')->group(function () {
            Route::get('recycle', 'recycle')->name('recycle');
            Route::post('{comment}/restore', 'restore')->name('restore');
            Route::delete('{comment}/force-delete', 'forceDelete')->name('force-delete');
            Route::delete('{comment}', 'softDelete')->name('soft-delete');
        });

        Route::put('{comment}', 'update')->name('update');
    });

