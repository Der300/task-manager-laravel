<?php

use App\Http\Controllers\Task\TaskController;

Route::middleware(['auth', 'verified'])
    ->prefix('tasks')
    ->name('tasks.')
    ->controller(TaskController::class)
    ->group(function () {
        Route::get('show/{task}', 'show')->name('show');
        Route::get('/', 'index')->name('index');

        Route::middleware('role:admin|super-admin|manager|leader|member')->group(function () {
            Route::get('edit/{task}', 'edit')->name('edit');
            Route::put('update/{task}', 'update')->name('update');
        });

        Route::middleware('role:admin|super-admin|manager|leader')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::post('assign/{task}', 'assign')->name('assign');

            Route::middleware('role:admin|super-admin|manager')->group(function () {
                Route::delete('soft-delete/{task}', 'softDelete')->name('soft-delete');
                Route::post('restore/{task}', 'restore')->name('restore');
                Route::delete('force-delete/{task}', 'forceDelete')->name('force-delete');
            });
        });
    });
