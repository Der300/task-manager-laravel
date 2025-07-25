<?php

use App\Http\Controllers\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('projects')
    ->name('projects.')
    ->controller(ProjectController::class)
    ->group(function () {
        Route::get('{project}', 'show')->name('show');
        Route::get('/', 'index')->name('index');

        Route::middleware('role:admin|super-admin|manager')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{project}/edit', 'edit')->name('edit');
            Route::put('{project}', 'update')->name('update');

            Route::middleware('role:admin|super-admin')->group(function () {
                Route::get('recycle', 'recycle')->name('recycle');
                Route::delete('{project}', 'softDelete')->name('soft-delete');
                Route::post('{project}/restore', 'restore')->name('restore');
                Route::delete('{project}/force-delete', 'forceDelete')->name('force-delete');

            });
        });
    });
