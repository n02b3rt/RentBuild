<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientRentalController;


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

Route::post('/client/rentals', [ClientRentalController::class, 'store'])
    ->middleware('auth')
    ->name('client.rentals.store');

Route::get('/client/rentals/summary/{equipment}', [ClientRentalController::class, 'summary'])
    ->middleware('auth')
    ->name('client.rentals.summary');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
