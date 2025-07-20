<?php

use App\Http\Controllers\Comment\CommentController;

Route::middleware(['auth', 'verified'])
    ->prefix('tasks/{task}')
    ->name('tasks.comments.')
    ->controller(CommentController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('edit/{comment}', 'edit')->name('edit');
        Route::put('update/{comment}', 'update')->name('update');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');

        Route::middleware('role:admin|super-admin')->group(function () {
            Route::delete('soft-delete/{comment}', 'softDelete')->name('soft-delete');
            Route::post('restore/{comment}', 'restore')->name('restore');
            Route::delete('force-delete/{comment}', 'forceDelete')->name('force-delete');
        });
    });
