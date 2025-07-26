<?php

use App\Http\Controllers\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('projects')
    ->name('projects.')
    ->controller(ProjectController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::middleware('role:admin|super-admin|manager')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('make-slug', 'makeSlug')->name('make_slug');
            Route::get('recycle', 'recycle')->name('recycle');

            Route::post('{project}/restore', 'restore')->withTrashed()->name('restore');
            Route::delete('{project}/force-delete', 'forceDelete')->withTrashed()->middleware('role:admin|super-admin', 'password.confirm')->name('force-delete');

            Route::put('{project}', 'update')->name('update');
            Route::delete('{project}', 'softDelete')->name('soft-delete');
        });

        Route::get('{project}', 'show')->name('show');
    });
