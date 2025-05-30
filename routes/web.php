<?php

use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\Admin\AdminEquipmentController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PromotionAddController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

// Strony publiczne
Route::get('/', fn() => view('welcome'));

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');
//Route::get('/equipment/gallery/{id}', [EquipmentController::class, 'showWithGallery'])->name('equipment.gallery');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard przekierowujący wg roli
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return $user->hasRole('administrator')
            ? Redirect::route('admin.dashboard')
            : Redirect::route('client.rentals.index');
    })->name('dashboard');

    // Client routes
    Route::prefix('client')->name('client.')->group(function () {

        // Rentals
        Route::prefix('rentals')->name('rentals.')->controller(ClientRentalController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/summary/{equipment}', 'summary')->name('summary');
            Route::post('/', 'store')->name('store');

            Route::get('/payment', 'payment')->name('payment');
            Route::post('/payment', 'processPayment')->name('processPayment');

            Route::post('/{rental}/cancel', 'cancel')->name('cancel');
            Route::post('/{rental}/end', 'end')->name('end');
        });

        // Account top-up
        Route::prefix('account')->name('account.')->controller(ClientAccountController::class)->group(function () {
            Route::get('/topup', 'showTopupForm')->name('topup.form');
            Route::post('/topup', 'processTopup')->name('topup.process');
        });
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

// Admin routes
Route::prefix('admin/dashboard')->name('admin.')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard view
    Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');

    // Equipment - użycie resource zamiast pojedynczych tras
    Route::resource('equipment', AdminEquipmentController::class)->except(['show']);

    // Promotions
    Route::prefix('promotions/category')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('promotions.category');
        Route::delete('{category}/delete', [PromotionController::class, 'destroyCategoryPromotion'])->name('promotions.delete');
        Route::get('add', [PromotionAddController::class, 'create'])->name('promotions.add');
        Route::post('add', [PromotionAddController::class, 'store'])->name('promotions.store');
    });
});

Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function() {
    Route::get('operator-rates',        [\App\Http\Controllers\Admin\OperatorRateController::class, 'index'])
        ->name('operator-rates.index');
    Route::get('operator-rates/{category}/edit', [\App\Http\Controllers\Admin\OperatorRateController::class, 'edit'])
        ->name('operator-rates.edit');
    Route::put('operator-rates/{category}',      [\App\Http\Controllers\Admin\OperatorRateController::class, 'update'])
        ->name('operator-rates.update');
});

require __DIR__.'/auth.php';
