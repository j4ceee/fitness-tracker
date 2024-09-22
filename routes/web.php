<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// get the view with the HomeController class
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('my-fitness/dashboard', function () {
    return view('dashboard');})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // viewing days
    Route::get('/days/{userId}/{page?}', [DayController::class, 'index'])
        ->name('days.index');
    Route::get('/day/{userId}/{date}', [DayController::class, 'indexDay'])
        ->name('days.day');

    Route::get('/my-fitness/{page?}', [DayController::class, 'indexMy'])
        ->name('days.my');
    Route::put('/my-fitness/add-today', [DayController::class, 'store'])
        ->name('days.my.create');

    Route::get('/my-fitness/{id}/edit', [DayController::class, 'edit'])
        ->name('days.edit');
    Route::patch('/my-fitness/{id}/update', [DayController::class, 'update'])
        ->name('days.update');
});

// image route
Route::get('/images/{imageName}', [ImageController::class, 'show'])->name('image.show');

/**
 * Admins only functions
 */
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    /**
     * Editing of users
     */
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::patch('/users/{id}', [UserController::class, 'update'])
        ->name('users.update');

    /**
     * Creation of users
     */
    Route::get('/users/add', [UserController::class, 'create'])
        ->name('users.create');
    Route::post('/users/add', [UserController::class, 'store'])
        ->name('users.store');

    /**
     * Deletion of users
     */
    Route::delete('/users/{id}/delete', [UserController::class, 'destroy'])
        ->name('users.destroy');
});

require __DIR__.'/auth.php';
