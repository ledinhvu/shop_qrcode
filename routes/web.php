<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::middleware('auth.basic')->group(function () {
    Route::get('/', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/create', [FileController::class, 'create'])->name('files.create');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
});