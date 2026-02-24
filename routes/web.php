<?php

use App\Http\Controllers\CafeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\RoasteryController;
use App\Http\Controllers\SavedController;
use Illuminate\Support\Facades\Route;

// Public routes with rate limiting
Route::middleware(['throttle:web'])->group(function () {
    Route::get('/', [CafeController::class, 'index'])->name('home');
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
    Route::get('/saved', [SavedController::class, 'index'])->name('saved');
    Route::get('/information', [InformationController::class, 'index'])->name('information');
    Route::get('/information/{information:slug}', [InformationController::class, 'show'])->name('information.show');
    Route::get('/profile', fn () => redirect()->route('information'))->name('profile');
    Route::get('/roastery', [RoasteryController::class, 'index'])->name('roastery');
    Route::get('/roastery/{roastery}', [RoasteryController::class, 'show'])->name('roastery.show');
    Route::get('/cafes/{cafe}', [CafeController::class, 'show'])->name('cafes.show');
    Route::redirect('/cafes', '/explore'); // Ensure no 404 for list request
});
