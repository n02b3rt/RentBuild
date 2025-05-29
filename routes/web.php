<?php

use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\Admin\AdminEquipmentController;
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
//Route::get('/equipment/gallery/{id}', [EquipmentController::class, 'showWithGallery'])->name('equipment.gallery');

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

        Route::post('/{rental}/cancel', [ClientRentalController::class, 'cancel'])->name('cancel');
        Route::post('/{rental}/end', [ClientRentalController::class, 'end'])->name('end');
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
})->middleware(['auth', 'verified'])->name('admin.dashboard');

// Trasy admina dla sprzętu
Route::prefix('admin/dashboard')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/equipment', [AdminEquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/create', [AdminEquipmentController::class, 'create'])->name('equipment.create');
    Route::post('/equipment', [AdminEquipmentController::class, 'store'])->name('equipment.store');
    Route::get('/equipment/{id}/edit', [AdminEquipmentController::class, 'edit'])->name('equipment.edit');
    Route::put('/equipment/{id}', [AdminEquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/equipment/{id}', [AdminEquipmentController::class, 'destroy'])->name('equipment.destroy');
});


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

Route::prefix('admin/dashboard/users')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::get('/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::match(['put', 'patch'], '/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
});


require __DIR__.'/auth.php';
