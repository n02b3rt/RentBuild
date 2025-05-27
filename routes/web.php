<?php

use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Publiczne trasy
Route::get('/', function () {
    return view('welcome');
});

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');

// Trasy wymagające uwierzytelnienia
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Wypożyczenia klienta
    Route::prefix('client/rentals')->name('client.rentals.')->group(function () {
        Route::get('/', [ClientRentalController::class, 'index'])->name('index');
        Route::get('/summary/{equipment}', [ClientRentalController::class, 'summary'])->name('summary');
        Route::post('/', [ClientRentalController::class, 'store'])->name('store');

        Route::get('/payment', [ClientRentalController::class, 'payment'])->name('payment');
        Route::post('/payment', [ClientRentalController::class, 'processPayment'])->name('processPayment');
    });

    // Konto klienta - doładowanie
    Route::prefix('client/account')->name('client.account.')->group(function () {
        Route::get('/topup', [ClientAccountController::class, 'showTopupForm'])->name('topup.form');
        Route::post('/topup', [ClientAccountController::class, 'processTopup'])->name('topup.process');
    });

    // Profil użytkownika
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
