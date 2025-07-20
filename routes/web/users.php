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
        Route::middleware('role:admin|super-admin|manager|leader|member|client')->group(function () {
            Route::get('show/{user}', 'show')->name('show');

            Route::get('edit/{user}', 'edit')->name('edit');
            Route::put('update/{user}', 'update')->name('update');
        });

        Route::middleware('role:admin|super-admin')->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');

            Route::post('assign/{user}', 'assign')->name('assign');

            Route::delete('soft-delete/{user}', 'softDelete')->name('soft-delete');
            Route::post('restore/{user}', 'restore')->name('restore');
            Route::delete('force-delete/{user}', 'forceDelete')->name('force-delete');
        });
    });
