<?php

use App\Http\Controllers\NotificationController;

Route::middleware(['auth', 'verified'])
    ->prefix('notifications')
    ->name('notifications.')
    ->controller(NotificationController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{id}/read', 'markAsRead')->name('read');
    });
