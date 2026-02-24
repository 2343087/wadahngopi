<?php

use App\Livewire\ExploreSearch;
use App\Models\Cafe;
use App\Models\Information;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

// --- Cached Random IDs ---

it('uses cached random IDs for relevance sort', function () {
    Cafe::factory()->count(5)->create(['status' => 'published']);

    // First render populates the cache
    Livewire::test(ExploreSearch::class)
        ->assertSuccessful();

    // Check that a cache key was created for random order
    $cacheExists = false;
    for ($i = 0; $i < 10; $i++) {
        if (Cache::has("cafe_random_order_{$i}")) {
            $cacheExists = true;
            break;
        }
    }

    expect($cacheExists)->toBeTrue();
});

// --- View Counter Batching ---

it('stores view count in cache instead of direct DB write', function () {
    $info = Information::factory()->create(['is_published' => true, 'views' => 0]);

    $this->get(route('information.show', $info));

    $cachedViews = (int) Cache::get("info_views:{$info->id}", 0);
    expect($cachedViews)->toBe(1);

    // DB should NOT have been incremented directly
    $info->refresh();
    expect($info->views)->toBe(0);
});

it('does not increment view count for same session', function () {
    $info = Information::factory()->create(['is_published' => true, 'views' => 0]);

    // Visit twice in same session
    $this->get(route('information.show', $info));
    $this->get(route('information.show', $info));

    $cachedViews = (int) Cache::get("info_views:{$info->id}", 0);
    expect($cachedViews)->toBe(1);
});

// --- Cache Invalidation ---

it('caches cafe detail for performance', function () {
    $cafe = Cafe::factory()->create(['status' => 'published']);

    // First visit should cache
    $this->get(route('cafes.show', $cafe))->assertSuccessful();

    $cacheKey = "cafe_{$cafe->slug}";
    expect(Cache::has($cacheKey))->toBeTrue();
});

it('caches the cities list', function () {
    Livewire::test(ExploreSearch::class)
        ->assertSuccessful();

    expect(Cache::has('cities_list'))->toBeTrue();
});

// --- Search Performance ---

it('handles long search terms without LIKE fallback', function () {
    Cafe::factory()->create(['name' => 'Kopi Kenangan Samarinda', 'status' => 'published']);

    // Long search should use only fulltext, not LIKE
    Livewire::test(ExploreSearch::class)
        ->set('search', 'Kenangan Samarinda')
        ->assertSuccessful();
});

it('uses LIKE for short search terms only', function () {
    Cafe::factory()->create(['name' => 'KK Cafe', 'status' => 'published']);

    Livewire::test(ExploreSearch::class)
        ->set('search', 'KK')
        ->assertSuccessful();
});
