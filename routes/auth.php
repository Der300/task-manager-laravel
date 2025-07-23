<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\SendResetPasswordLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// middleware guest: vendor\laravel\framework\src\Illuminate\Auth\Middleware\RedirectIfAuthenticated.php
Route::middleware('guest')->group(function () {
    //Login
    Route::controller(AuthenticatedSessionController::class)->group(function () {
        Route::get('login', 'create')->name('login');
        Route::post('login', 'store');
    });

    //Forgot Password
    Route::controller(PasswordResetLinkController::class)->group(function () {
        Route::get('forgot-password', 'create')->name('password.request');
        Route::post('forgot-password', 'store')->name('password.email');
    });

    //Reset Password
    Route::controller(NewPasswordController::class)->group(function () {
        Route::get('reset-password/{token}', 'create')->name('password.reset');
        Route::post('reset-password', 'store')->name('password.store');
    });
});


//vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php
Route::middleware('auth')->group(function () {
    //Gửi link reset cho user khác (admin, super-admin, manager, leader)
    Route::post(
        'users/{user}/send-reset-link',
        [SendResetPasswordLinkController::class, 'sendResetLink']
    )->middleware('role:admin|super-admin|manager|leader')->name('users.send-reset-link');

    //Xác minh email
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

    Route::controller(VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->group(function () {
        Route::get('verify-email/{id}/{hash}', '__invoke')->name('verification.verify');
    });

    Route::controller(EmailVerificationNotificationController::class)->middleware('throttle:6,1')->group(function () {
        Route::post('email/verification-notification', 'store')->name('verification.send');
    });

    //Xác nhận mật khẩu
    Route::controller(ConfirmablePasswordController::class)->group(function () {
        Route::get('confirm-password', 'show')->name('password.confirm');
        Route::post('confirm-password', 'store');
    });

    //Cập nhật mật khẩu (người dùng tự thay)
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    //Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
