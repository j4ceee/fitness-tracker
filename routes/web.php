<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// get the view with the HomeController class
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// image route
Route::get('/images/{imageName}', [ImageController::class, 'show'])->name('image.show');

/**
 * Admins only functions
 */
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});

require __DIR__.'/auth.php';
