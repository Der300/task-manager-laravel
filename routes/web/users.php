<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('users')
    ->name('users.')
    ->controller(UserController::class)
    ->group(function () {
        Route::middleware('role:admin|super-admin|manager|leader|member')->group(function () {
            Route::get('/', 'index')->name('index');
        });

        Route::middleware('role:admin|super-admin')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');

            Route::middleware('password.confirm')->group(function () {
                Route::get('recycle', 'recycle')->name('recycle');

                Route::delete('{user}', 'softDelete')->name('soft-delete');
                Route::post('{user}/restore', 'restore')->withTrashed()->name('restore');
                Route::delete('{user}/force-delete', 'forceDelete')->withTrashed()->name('force-delete');
            });
        });

        Route::put('{user}', 'update')->name('update');
        Route::get('{user}', 'show')->name('show');
        Route::get('{user}/edit', 'edit')->name('edit');
    });
