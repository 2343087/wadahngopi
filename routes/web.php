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

    // WFC Scoring (Protected)
    Route::post('/cafes/{cafe:id}/wfc-score', [\App\Http\Controllers\Api\WfcScoreController::class, 'store'])
        ->middleware('auth')
        ->name('cafes.wfc-score');

    // Vibe Meter API (Public read, rate-limited write)
    Route::get('/cafes/{cafe:id}/vibe', [\App\Http\Controllers\Api\VibeController::class, 'show'])
        ->name('cafes.vibe.show');
    Route::post('/cafes/{cafe:id}/vibe', [\App\Http\Controllers\Api\VibeController::class, 'store'])
        ->name('cafes.vibe.store');

    // Bookmark API (Auth Required)
    Route::middleware('auth')->prefix('api/bookmarks')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\BookmarkController::class, 'index'])->name('bookmarks.index');
        Route::post('/toggle', [\App\Http\Controllers\Api\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
        Route::post('/sync', [\App\Http\Controllers\Api\BookmarkController::class, 'sync'])->name('bookmarks.sync');
    });

    // Check-in & Badges (Auth Required)
    Route::middleware('auth')->group(function () {
        Route::post('/cafes/{cafe:id}/check-in', [\App\Http\Controllers\Api\CheckInController::class, 'store'])
            ->name('cafes.check-in');
        Route::get('/api/badges', [\App\Http\Controllers\Api\CheckInController::class, 'badges'])
            ->name('badges.index');
        Route::get('/profile/badges', function () {
            return view('profile.badges');
        })->name('profile.badges');
    });

    // Tongkrongan (Public — no auth required)
    Route::get('/tongkrongan/search-cafes', [\App\Http\Controllers\TongkronganController::class, 'searchCafes'])
        ->name('tongkrongan.search-cafes');
    Route::get('/tongkrongan/buat', [\App\Http\Controllers\TongkronganController::class, 'create'])
        ->name('tongkrongan.create');
    Route::post('/tongkrongan', [\App\Http\Controllers\TongkronganController::class, 'store'])
        ->name('tongkrongan.store');
    Route::get('/tongkrongan/{tongkrongan:uuid}', [\App\Http\Controllers\TongkronganController::class, 'show'])
        ->name('tongkrongan.show');
    Route::get('/tongkrongan/{tongkrongan:uuid}/votes', [\App\Http\Controllers\TongkronganController::class, 'getVotes'])
        ->name('tongkrongan.votes');
    Route::post('/tongkrongan/{tongkrongan:uuid}/vote/{item}', [\App\Http\Controllers\TongkronganController::class, 'vote'])
        ->name('tongkrongan.vote');

    // Auth Actions
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
