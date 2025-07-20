<?php

use App\Http\Controllers\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('projects')
    ->name('projects.')
    ->controller(ProjectController::class)
    ->group(function () {
        Route::get('show/{project}', 'show')->name('show');

        Route::get('/', 'index')
            ->middleware('role:admin|super-admin|manager|leader|member')
            ->name('index');

        Route::middleware('role:admin|super-admin|manager')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{project}', 'edit')->name('edit');
            Route::put('update/{project}', 'update')->name('update');

            Route::post('assign/{project}', 'assign')->name('assign');

            Route::middleware('role:admin|super-admin')->group(function () {
                Route::delete('soft-delete/{project}', 'softDelete')->name('soft-delete');
                Route::post('restore/{project}', 'restore')->name('restore');
                Route::delete('force-delete/{project}', 'forceDelete')->name('force-delete');
            });
        });
    });
