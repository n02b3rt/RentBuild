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
        return Redirect::route('client.rentals.index');
    })->middleware(['auth', 'verified'])->name('dashboard');

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


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// ===== Promocje =====

Route::prefix('admin/dashboard')->name('admin.')->group(function () {
    // Strona z wszystkimi promocjami pogrupowanymi po kategoriach
    Route::get('promotions/category', [PromotionController::class, 'index'])->name('promotions.category');
});

Route::delete('admin/dashboard/promotions/category/{category}/delete', [PromotionController::class, 'destroyCategoryPromotion'])
    ->name('admin.promotions.delete');


Route::prefix('admin/dashboard')->name('admin.')->group(function () {
    // Form (GET)
    Route::get('promotions/category/add', [PromotionAddController::class, 'create'])->name('promotions.add');

    // Submit form (POST)
    Route::post('promotions/category/add', [PromotionAddController::class, 'store'])->name('promotions.store');
});

require __DIR__.'/auth.php';
