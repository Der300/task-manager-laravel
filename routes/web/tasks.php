<?php

use App\Http\Controllers\Task\TaskController;

Route::middleware(['auth', 'verified'])
    ->prefix('tasks')
    ->name('tasks.')
    ->controller(TaskController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::middleware('role:admin|super-admin|manager|leader')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');

            Route::get('recycle', 'recycle')->name('recycle');
            Route::delete('{task}', 'softDelete')->name('soft-delete');
            Route::post('{task}/restore', 'restore')->withTrashed()->name('restore');

            Route::delete('{task}/force-delete', 'forceDelete')->withTrashed()->middleware('role:admin|super-admin', 'password.confirm')->name('force-delete');
        });

        Route::middleware('role:admin|super-admin|manager|leader|member')->group(function () {
            Route::put('{task}', 'update')->name('update');
        });

        Route::get('{task}', 'show')->name('show');
    });
