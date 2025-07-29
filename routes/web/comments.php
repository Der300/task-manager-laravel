<?php

use App\Http\Controllers\Comment\CommentController;

Route::middleware(['auth', 'verified'])
    ->prefix('comments')
    ->name('comments.')
    ->controller(CommentController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::delete('{comment}/force-delete', 'forceDelete')->middleware('role:admin|super-admin', 'password.confirm')->withTrashed()->name('force-delete');

        Route::get('recycle', 'recycle')->name('recycle');
        Route::post('{comment}/restore', 'restore')->withTrashed()->name('restore');
        Route::delete('{comment}', 'softDelete')->name('soft-delete');
        
        Route::put('{comment}', 'update')->name('update');
        Route::post('{task}/new-comments', 'store')->name('store');
    });
