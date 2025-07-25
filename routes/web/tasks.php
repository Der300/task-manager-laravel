<?php

use App\Http\Controllers\Task\TaskController;

Route::middleware(['auth', 'verified'])
    ->prefix('tasks')
    ->name('tasks.')
    ->controller(TaskController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{task}', 'show')->name('show');

        Route::middleware('role:admin|super-admin|manager|leader')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');

            Route::middleware('role:admin|super-admin|manager')->group(function () {
                Route::get('recycle', 'recycle')->name('recycle');
                Route::delete('{task}', 'softDelete')->name('soft-delete');
                Route::post('{task}/restore', 'restore')->name('restore');
                Route::delete('{task}/force-delete', 'forceDelete')->name('force-delete');
            });
        });

        Route::middleware('role:admin|super-admin|manager|leader|member')->group(function () {
            Route::get('{task}/edit', 'edit')->name('edit');
            Route::put('{task}', 'update')->name('update');
        });
    });
