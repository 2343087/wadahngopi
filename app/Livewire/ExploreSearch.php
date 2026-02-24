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
        if (! in_array($this->filter, ['semua', 'buka', 'terdekat'])) {
            $this->filter = 'semua';
        }

        if ($this->filter === 'terdekat') {
            $this->dispatch('request-location');
        }
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        if (! in_array($this->sort, ['relevance', 'name_az', 'name_za', 'distance'])) {
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
        if ($this->perPage >= 120) {
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
            fn () => City::select(['id', 'name'])->orderBy('name')->get()
        );
    }

    public function render()
    {
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

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->search) {
            $query->search($this->search);
        }

        // Letter Filter
        if ($this->activeLetter) {
            $query->where('name', 'like', $this->activeLetter.'%');
        }

        // Filter Logic for "buka" - uses centralized service logic
        if ($this->filter === 'buka') {
            $query->openNow();
        } elseif ($this->filter === 'terdekat' && $this->userLat !== null && $this->userLng !== null) {
            $query->nearest($this->userLat, $this->userLng);
        }

        // Sort Logic (if not already sorted by distance via filter)
        if ($this->filter !== 'terdekat') {
            if ($this->sort === 'name_az') {
                $query->orderBy('name', 'asc');
            } elseif ($this->sort === 'name_za') {
                $query->orderBy('name', 'desc');
            } elseif (! $this->search && ! $this->activeLetter && $this->sort === 'relevance') {
                // Fair Play: Cached random order (refreshes every 5 minutes)
                $randomIds = \Illuminate\Support\Facades\Cache::remember(
                    'cafe_random_order_'.($this->randomSeed % 10),
                    now()->addMinutes(5),
                    fn () => Cafe::where('status', 'published')->pluck('id')->shuffle()->toArray()
                );

                if (! empty($randomIds)) {
                    $idList = implode(',', array_map('intval', $randomIds));
                    $query->orderByRaw("FIELD(id, {$idList})");
                }
            } else {
                $query->latest();
            }
        }

        $cafesPaginator = $query->paginate($this->perPage);

        // Dispatch events for map update if needed
        $this->dispatch('cafes-updated', cafes: collect($cafesPaginator->items())->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'lat' => $c->latitude,
            'lng' => $c->longitude,
            'url' => route('cafes.show', $c),
        ])->toArray());

        return view('livewire.explore-search', [
            'cafes' => $cafesPaginator,
            'cities' => $this->cities,
        ]);
    }
}
