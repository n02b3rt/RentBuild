<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SprzetController;
use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\AdminRentalController;


Route::get('/', function () {
    return view('home');
});

Route::get('/sprzety', [SprzetController::class, 'index'])->name('sprzety.index');

Route::get('/sprzety/{id}', [SprzetController::class, 'show'])->name('sprzety.pokaz');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.klient');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->name('admin.dashboard');
});

// Panel klienta – wypożyczenia
Route::middleware(['auth', 'verified'])->prefix('client')->group(function () {
    Route::get('/rentals', [ClientRentalController::class, 'index'])->name('client.rentals.index');
    Route::post('/rentals', [ClientRentalController::class, 'store'])->name('client.rentals.store');
    Route::get('/rentals/{rental}', [ClientRentalController::class, 'show'])->name('client.rentals.show');
    Route::post('/rentals/{rental}/return', [ClientRentalController::class, 'return'])->name('client.rentals.return');
    Route::post('/rentals/{rental}/cancel', [ClientRentalController::class, 'cancel'])->name('client.rentals.cancel');
});

// Panel admina – wypożyczenia
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/rentals', [AdminRentalController::class, 'index'])->name('admin.rentals.index');
    Route::get('/rentals/{rental}/edit', [AdminRentalController::class, 'edit'])->name('admin.rentals.edit');
    Route::put('/rentals/{rental}', [AdminRentalController::class, 'update'])->name('admin.rentals.update');
});

require __DIR__.'/auth.php';
