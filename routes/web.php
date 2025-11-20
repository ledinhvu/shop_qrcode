<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::middleware('auth.basic')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');
});