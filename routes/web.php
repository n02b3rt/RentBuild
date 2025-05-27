<?php

use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/client/rentals', [ClientRentalController::class, 'index'])->name('client.rentals.index');

    Route::get('/client/rentals/summary/{equipment}', [ClientRentalController::class, 'summary'])->name('client.rentals.summary');

    Route::post('/client/rentals', [ClientRentalController::class, 'store'])->name('client.rentals.store');

    Route::get('/client/rentals/payment', [ClientRentalController::class, 'payment'])->name('client.rentals.payment');

    Route::post('/client/rentals/payment', [ClientRentalController::class, 'processPayment'])->name('client.rentals.processPayment');

    Route::get('/client/account/topup', [ClientAccountController::class, 'showTopupForm'])->name('client.account.topup.form');
    Route::post('/client/account/topup', [ClientAccountController::class, 'processTopup'])->name('client.account.topup.process');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
