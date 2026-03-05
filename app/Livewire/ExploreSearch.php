<?php

namespace App\Livewire;

use App\Models\Cafe;
use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class ExploreSearch extends Component
{
    use WithPagination;

    public int $perPage = 12;

    public const MAX_PER_PAGE = 120;

    public string $search = '';

    public string $filter = 'semua';

    public string $sort = 'relevance';

    public ?string $activeLetter = null;

    public ?int $cityId = null;

    public ?float $userLat = null;

    public ?float $userLng = null;

    public ?int $randomSeed = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'cityId' => ['except' => null],
        'filter' => ['except' => 'semua'],
        'sort' => ['except' => 'relevance'],
        'activeLetter' => ['except' => null],
    ];

    protected $rules = [
        'search' => 'max:100',
    ];

    public function mount(): void
    {
        $this->randomSeed = rand(1, 999999);
    }

    public function updatedSearch(): void
    {
        $this->validate(['search' => 'max:100']);
        $this->perPage = 12;
        $this->resetPage();
    }

    public function updatedCityId(): void
    {
        $this->validate(['cityId' => 'nullable|exists:cities,id']);
        $this->perPage = 12;
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        if (!in_array($this->filter, ['semua', 'buka', 'terdekat'])) {
            $this->filter = 'semua';
        }

        if ($this->filter === 'terdekat') {
            $this->dispatch('request-location');
        }
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        if (!in_array($this->sort, ['relevance', 'name_az', 'name_za', 'distance'])) {
            $this->sort = 'relevance';
        }

        $this->resetPage();
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
        // When explicitly setting sort (e.g. from dropdown), clear the specific letter filter
        // to avoid "getting stuck" on a previously selected letter.
        if (in_array($sort, ['name_az', 'name_za'])) {
            $this->activeLetter = null;
        }
        $this->resetPage();
    }

    public function updatedActiveLetter(): void
    {
        $this->resetPage();
        // If a letter is selected, it implies sorting by name usually, but let's just filter.
        // Optionally, force sort to name_az if logic dictates.
        if ($this->activeLetter) {
            $this->sort = 'name_az';
        }
    }

    public function setLetter(?string $letter): void
    {
        if ($this->activeLetter === $letter) {
            $this->activeLetter = null; // Toggle off
        } else {
            $this->activeLetter = $letter;
            $this->sort = 'name_az'; // Auto-sort A-Z when picking a letter
        }
        $this->resetPage();
    }

    /**
     * Reset all filters to their default state.
     */
    public function loadMore(): void
    {
        if ($this->perPage >= self::MAX_PER_PAGE) {
            return;
        }
        $this->perPage += 12;
    }

    public function resetAllFilters(): void
    {
        $this->search = '';
        $this->filter = 'semua';
        $this->sort = 'relevance';
        $this->activeLetter = null;
        $this->cityId = null;
        $this->userLat = null;
        $this->userLng = null;
        $this->perPage = 12;
        $this->resetPage();
    }

    public function setUserLocation(float $lat, float $lng): void
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        // If filter is currently 'terdekat', we can now process it correctly
        if ($this->filter === 'terdekat') {
            $this->sort = 'distance'; // Auto switch sort to distance
        }
    }

    public function getCitiesProperty()
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'cities_list',
            now()->addHour(),
            fn() => City::select(['id', 'name'])->orderBy('name')->get()
        );
    }

    public function render()
    {
        // 1. Initial Query with Essential Fields
        $query = Cafe::query()
            ->where('status', 'published')
            ->select([
                'id',
                'name',
                'slug',
                'city_id',
                'address',
                'latitude',
                'longitude',
                'image_path',
                'social_links',
                'is_24_hours',
                'operating_hours',
                'weekday_open',
                'weekday_close',
                'weekend_open',
                'weekend_close',
            ])
            ->with(['facilities:id,cafe_id,name', 'city:id,name']);

        // 2. Apply Filters & Location
        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }
        if ($this->search) {
            $query->search($this->search);
        }
        if ($this->activeLetter) {
            $query->where('name', 'like', $this->activeLetter . '%');
        }
        if ($this->filter === 'buka') {
            $query->openNow();
        } elseif ($this->filter === 'terdekat' && $this->userLat !== null && $this->userLng !== null) {
            $query->nearest($this->userLat, $this->userLng);
        }

        // 3. Robust Total Counting (Cached to save DB load at 50k scale)
        $locationHash = ($this->userLat && $this->userLng) ? round($this->userLat, 2) . ',' . round($this->userLng, 2) : 'none';
        $cacheKeyTotal = 'total_v8_' . md5($this->cityId . $this->search . $this->activeLetter . $this->filter . $locationHash);
        $totalResults = \Illuminate\Support\Facades\Cache::remember($cacheKeyTotal, now()->addMinutes(5), fn() => $query->count());

        // 4. Optimized Sort & Randomization
        $isHomeRandom = ($this->sort === 'relevance' && !$this->search && !$this->activeLetter);

        if ($isHomeRandom) {
            // High Performance Randomization: Database-agnostic seeded random
            $driver = $query->getConnection()->getDriverName();
            if ($driver === 'sqlite') {
                $query->orderByRaw('ABS(RANDOM()) % ?', [$this->randomSeed ?: 1000]);
            } else {
                $query->orderByRaw('RAND(?)', [$this->randomSeed]);
            }
        } else {
            // Standard Sorts
            if ($this->sort === 'name_az') {
                $query->orderBy('name', 'asc');
            } elseif ($this->sort === 'name_za') {
                $query->orderBy('name', 'desc');
            } else {
                // If special filter (like nearest) is not active, fallback to latest
                if ($this->filter !== 'terdekat') {
                    $query->latest();
                }
            }
        }

        // 5. Final Result Paginator
        // Force Page 1 because we grow perPage for a seamless infinite scroll experience
        $cafesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->limit($this->perPage)->get(),
            $totalResults,
            $this->perPage,
            1,
            ['path' => route('explore')]
        );

        // 6. Map Update (Limited to avoid choking the browser)
        if ($this->perPage <= 24 || $this->search) {
            $this->dispatch('cafes-updated', cafes: collect($cafesPaginator->items())->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'lat' => $c->latitude,
                'lng' => $c->longitude,
                'url' => route('cafes.show', $c),
            ])->toArray());
        }

        return view('livewire.explore-search', [
            'cafes' => $cafesPaginator,
            'cities' => $this->cities,
        ]);
    }
}
