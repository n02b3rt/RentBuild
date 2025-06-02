<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\Admin\AdminEquipmentController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PromotionAddController;
use App\Http\Controllers\Admin\SinglePromotionController;
use App\Http\Controllers\Admin\AdminRentalController;
use App\Http\Controllers\Client\RentalComplaintController as ClientRentalComplaintController;
use App\Http\Controllers\Admin\RentalComplaintController as AdminRentalComplaintController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Middleware\EnsureTwoFactorIsVerified;

// Strony publiczne
Route::get('/', fn() => view('welcome'));

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');
Route::get('/equipments/{id}/preview', [EquipmentController::class, 'showPreview'])->name('equipment.showPreview');

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
            Route::post('/payWithBiwo', 'payWithBiwo')->name('payWithBiwo');
        });

        // Account top-up
        Route::prefix('account')->name('account.')->controller(ClientAccountController::class)->group(function () {
            Route::get('/topup', 'showTopupForm')->name('topup.form');
            Route::post('/topup', 'processTopup')->name('topup.process');
        });
    });

    Route::prefix('profile/2fa')->name('2fa.')->controller(TwoFactorController::class)->group(function () {
        Route::get('recovery-codes', 'showRecoveryCodes')->name('recovery');
        Route::post('regenerate-codes', 'regenerateRecoveryCodes')->name('recovery.regenerate');
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });
});

// Admin routes
Route::prefix('admin/dashboard')->name('admin.')->middleware(['auth', 'verified', EnsureTwoFactorIsVerified::class])->group(function () {

    // Dashboard view
    Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');

    // Equipment
    Route::resource('equipment', AdminEquipmentController::class)->except(['show']);

    // Promotions - category
    Route::prefix('promotions/category')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('promotions.category');
        Route::delete('{category}/delete', [PromotionController::class, 'destroyCategoryPromotion'])->name('promotions.delete');
        Route::get('add', [PromotionAddController::class, 'create'])->name('promotions.add');
        Route::post('add', [PromotionAddController::class, 'store'])->name('promotions.store');
    });

    // Promotions - single equipment
    Route::prefix('promotions/single')->name('promotions.single.')->group(function () {
        Route::get('/', [SinglePromotionController::class, 'index'])->name('index');
        Route::get('/create', [SinglePromotionController::class, 'create'])->name('create');
        Route::post('/', [SinglePromotionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SinglePromotionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SinglePromotionController::class, 'update'])->name('update');
        Route::delete('/{id}', [SinglePromotionController::class, 'destroy'])->name('destroy');
    });

    // Operator rates
    Route::prefix('operator-rates')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OperatorRateController::class, 'index'])->name('operator-rates.index');
        Route::get('/{category}/edit', [\App\Http\Controllers\Admin\OperatorRateController::class, 'edit'])->name('operator-rates.edit');
        Route::put('/{category}', [\App\Http\Controllers\Admin\OperatorRateController::class, 'update'])->name('operator-rates.update');
    });

    // Users management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    });
});

// Reklamacje - klient
Route::middleware(['auth'])->prefix('client/rentals')->name('client.rentals.')->group(function () {
    Route::get('{rental}/complaint', [ClientRentalComplaintController::class, 'create'])->name('complaint.create');
    Route::post('{rental}/complaint', [ClientRentalComplaintController::class, 'store'])->name('complaint.store');
    Route::get('complaints', [ClientRentalComplaintController::class, 'index'])->name('complaints.index');
    Route::get('complaints/{rental}', [ClientRentalComplaintController::class, 'show'])->name('complaints.show');
});

Route::middleware(['auth'])->prefix('admin/rentals')->name('admin.rentals.')->group(function () {
    // Reklamacje - admin
    Route::get('complaints', [AdminRentalComplaintController::class, 'index'])->name('complaints.index');
    Route::get('complaints/{rental}', [AdminRentalComplaintController::class, 'show'])->name('complaints.show');
    Route::post('complaints/{rental}/resolve', [AdminRentalComplaintController::class, 'resolve'])->name('complaints.resolve');

    // Zamówienia - admin
    Route::get('list', [AdminRentalController::class, 'index'])->name('list.index');
    Route::get('create', [AdminRentalController::class, 'create'])->name('create');
    Route::get('show/{rental}', [AdminRentalController::class, 'show'])->name('show');
    Route::get('edit/{rental}', [AdminRentalController::class, 'edit'])->name('edit');
    Route::patch('/{rental}/approve', [AdminRentalController::class, 'approve'])->name('approve');
    Route::patch('/{rental}/cancel', [AdminRentalController::class, 'cancel'])->name('cancel');
    Route::patch('/{rental}/reject', [AdminRentalController::class, 'reject'])->name('reject');
    Route::patch('/{rental}/update', [AdminRentalController::class, 'update'])->name('update');
});


require __DIR__.'/auth.php';
