<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\GroupMiddleware;
use Illuminate\Support\Facades\Route;

// get the view with the HomeController class
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::middleware('auth')->group(function () {
    Route::redirect('/my-fitness/0', '/my-fitness');
    Route::get('/my-fitness/{day?}', [ProfileController::class, 'dashboard'])
        ->where('day', '[0-9]+')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // viewing my days (same as days.index but with the user id automatically set to the logged-in user)
    Route::redirect('/my-fitness/list/0', '/my-fitness/list');
    Route::get('/my-fitness/list/{page?}', [DayController::class, 'indexMy'])
        ->name('days.my')
        ->where('page', '[0-9]+');

    // creating and storing days
    Route::get('/my-fitness/add', [DayController::class, 'create'])
        ->name('days.create');
    Route::put('/my-fitness/add', [DayController::class, 'store'])
        ->name('days.store');

    // editing and updating days
    Route::get('/my-fitness/{id}/edit', [DayController::class, 'edit'])
        ->name('days.edit');
    Route::patch('/my-fitness/{id}/edit', [DayController::class, 'update'])
        ->name('days.update');

    // deleting days
    Route::delete('/my-fitness/{id}/delete', [DayController::class, 'destroy'])
        ->name('days.destroy');

    // viewing the leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])
        ->name('leaderboard');
});

// viewing other users' days
Route::middleware(['auth', GroupMiddleware::class])->group(function () {
    // viewing days
    Route::get('/days/{userId}/{page?}', [DayController::class, 'index'])
        ->name('days.index')
        ->where('userId', '[0-9]+')
        ->where('page', '[0-9]+');
    // viewing a single day
    Route::get('/day/{userId}/{date}', [DayController::class, 'indexDay'])
        ->name('days.day');
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
