<?php

use App\Http\Controllers\Auth\TwoFactorController;
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
use App\Http\Middleware\EnsureTwoFactorIsVerified;

// Strony publiczne
Route::get('/', fn() => view('welcome'));

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');

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

Route::prefix('admin/dashboard/users')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::get('/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::match(['put', 'patch'], '/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
});


require __DIR__.'/auth.php';
