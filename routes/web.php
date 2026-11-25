<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\VibeController;
use App\Http\Controllers\Api\WfcScoreController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\TongkronganController;
use Illuminate\Support\Facades\Route;

// Public routes with rate limiting
Route::middleware(['throttle:web'])->group(function () {
    Route::get('/', [PublicCatalogController::class, 'home'])->name('home');
    Route::get('/explore', [PublicCatalogController::class, 'cafes'])->name('explore');
    Route::get('/saved', [PublicCatalogController::class, 'saved'])->name('saved');
    Route::get('/information', [PublicCatalogController::class, 'information'])->name('information');
    Route::get('/information/{information:slug}', [PublicCatalogController::class, 'article'])->name('information.show');
    Route::get('/profile', fn () => redirect()->route('information'))->name('profile');
    Route::get('/roastery', [PublicCatalogController::class, 'roasteries'])->name('roastery');
    Route::get('/roastery/{roastery}', [PublicCatalogController::class, 'roastery'])->name('roastery.show');
    Route::get('/cafes/{cafe}', [PublicCatalogController::class, 'cafe'])->name('cafes.show');
    Route::get('/api/cafe-roulette', [PublicCatalogController::class, 'roulette'])->middleware('throttle:30,1');

    Route::middleware('guest')->group(function () {
        Route::view('/admin/login', 'vue', ['title' => 'Masuk ke WadahNgopi'])->name('login');
        Route::view('/admin/register', 'vue', ['title' => 'Daftar WadahNgopi'])->name('register');
        Route::post('/admin/login', [AccountController::class, 'login'])->middleware('throttle:login');
        Route::post('/admin/register', [AccountController::class, 'register'])->middleware('throttle:login');
    });

    Route::middleware('auth')->prefix('admin')->group(function () {
        Route::get('/', [CmsController::class, 'home'])->name('admin.home');
        Route::get('/data/{resource}', [CmsController::class, 'index']);
        Route::post('/data/{resource}', [CmsController::class, 'store']);
        Route::get('/data/{resource}/{id}', [CmsController::class, 'show'])->whereNumber('id');
        Route::patch('/data/{resource}/{id}', [CmsController::class, 'update'])->whereNumber('id');
        Route::delete('/data/{resource}/{id}', [CmsController::class, 'destroy'])->whereNumber('id');
    });
    Route::redirect('/cafes', '/explore'); // Ensure no 404 for list request

    // WFC Scoring (Protected)
    Route::post('/cafes/{cafe:id}/wfc-score', [WfcScoreController::class, 'store'])
        ->middleware('auth')
        ->name('cafes.wfc-score');

    // Vibe Meter API (Public read, rate-limited write)
    Route::get('/cafes/{cafe:id}/vibe', [VibeController::class, 'show'])
        ->name('cafes.vibe.show');
    Route::post('/cafes/{cafe:id}/vibe', [VibeController::class, 'store'])
        ->name('cafes.vibe.store');

    // Bookmark API (Auth Required)
    Route::middleware('auth')->prefix('api/bookmarks')->group(function () {
        Route::get('/', [BookmarkController::class, 'index'])->name('bookmarks.index');
        Route::post('/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
        Route::post('/sync', [BookmarkController::class, 'sync'])->name('bookmarks.sync');
    });

    // Check-in & Badges (Auth Required)
    Route::middleware('auth')->group(function () {
        Route::post('/cafes/{cafe:id}/check-in', [CheckInController::class, 'store'])
            ->name('cafes.check-in');
        Route::get('/api/badges', [CheckInController::class, 'badges'])
            ->name('badges.index');
        Route::get('/profile/badges', function () {
            return view('vue', ['title' => 'Lencana kopi']);
        })->name('profile.badges');
    });

    // Tongkrongan (Public — no auth required)
    Route::get('/tongkrongan/search-cafes', [TongkronganController::class, 'searchCafes'])
        ->name('tongkrongan.search-cafes');
    Route::get('/tongkrongan/buat', [TongkronganController::class, 'create'])
        ->name('tongkrongan.create');
    Route::post('/tongkrongan', [TongkronganController::class, 'store'])
        ->name('tongkrongan.store');
    Route::get('/tongkrongan/{tongkrongan:uuid}', [TongkronganController::class, 'show'])
        ->name('tongkrongan.show');
    Route::get('/tongkrongan/{tongkrongan:uuid}/votes', [TongkronganController::class, 'getVotes'])
        ->name('tongkrongan.votes');
    Route::post('/tongkrongan/{tongkrongan:uuid}/vote/{item}', [TongkronganController::class, 'vote'])
        ->name('tongkrongan.vote');

    // Auth Actions
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
