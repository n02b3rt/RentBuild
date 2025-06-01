<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\TwoFactorLoginController;;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::post('/reset-password', [PasswordResetController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('user/2fa/setup',    [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('user/2fa/generate',[TwoFactorController::class, 'generate'])->name('2fa.generate');
    Route::post('user/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post('user/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});

Route::get('2fa',  [TwoFactorLoginController::class, 'showLoginForm'])->name('2fa.login');
Route::post('2fa', [TwoFactorLoginController::class, 'verifyLogin']);

