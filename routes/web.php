<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminEquipmentController;

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

Route::prefix('admin/dashboard')->group(function () {
    Route::get('/equipments', [AdminEquipmentController::class, 'index'])->name('admin.equipment.index');
    Route::get('/equipments/create', [AdminEquipmentController::class, 'create'])->name('admin.equipment.create');
    Route::post('/equipments', [AdminEquipmentController::class, 'store'])->name('admin.equipment.store');

    // 🟡 Nowe ścieżki:
    Route::get('/equipments/{id}/edit', [AdminEquipmentController::class, 'edit'])->name('admin.equipment.edit');
    Route::put('/equipments/{id}', [AdminEquipmentController::class, 'update'])->name('admin.equipment.update');

    Route::delete('/equipments/{id}', [AdminEquipmentController::class, 'destroy'])->name('admin.equipment.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
