<?php

use App\Http\Controllers\CafeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\SavedController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CafeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/saved', [SavedController::class, 'index'])->name('saved');
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
Route::get('/cafes/{cafe}', [CafeController::class, 'show'])->name('cafes.show');
Route::post('/cafes/{cafe}/review', [CafeController::class, 'storeReview'])->name('cafes.review');
