<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware('guest')->get('/',[AuthenticatedSessionController::class,'create']);

require __DIR__ . '/auth.php';
require __DIR__ . '/web/dashboard.php';
require __DIR__ . '/web/comments.php';
require __DIR__ . '/web/projects.php';
require __DIR__ . '/web/tasks.php';
require __DIR__ . '/web/users.php';
require __DIR__ . '/web/notifications.php';
require __DIR__ . '/web/files.php';