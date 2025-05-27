<?php

use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PromotionAddController;
use Illuminate\Support\Facades\Route;

// Strony publiczne
Route::get('/', function () {
    return view('welcome');
});

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');

// Trasy wymagające uwierzytelnienia i weryfikacji emaila
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard użytkownika
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

// Panel admina - trasy wymagające uprawnień administratora
Route::middleware(['auth', 'verified', 'can:admin-access'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard admina
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Promocje
    Route::prefix('promotions')->name('promotions.')->group(function () {
        // Lista promocji pogrupowanych po kategoriach
        Route::get('category', [PromotionController::class, 'index'])->name('category');

        // Usuwanie promocji z kategorii
        Route::delete('category/{category}/delete', [PromotionController::class, 'destroyCategoryPromotion'])
            ->name('delete');

        // Dodawanie promocji - formularz i zapis
        Route::get('category/add', [PromotionAddController::class, 'create'])->name('add');
        Route::post('category/add', [PromotionAddController::class, 'store'])->name('store');
    });
});

require __DIR__.'/auth.php';
