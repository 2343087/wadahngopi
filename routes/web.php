<?php

use App\Http\Controllers\CafeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\SavedController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CafeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/saved', [SavedController::class, 'index'])->name('saved');
Route::get('/information', [InformationController::class, 'index'])->name('information');
Route::get('/information/{information:slug}', [InformationController::class, 'show'])->name('information.show');
Route::get('/profile', function () {
    return redirect()->route('information');
})->name('profile');
Route::get('/cafes/{cafe}', [CafeController::class, 'show'])->name('cafes.show');
