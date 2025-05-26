<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PromotionAddController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/{id}', [EquipmentController::class, 'show'])->name('equipment.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
