<?php

namespace App\Livewire;

use App\Models\Cafe;
use App\Models\City;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ExploreSearch extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'semua';

    public string $sort = 'relevance';

    public ?string $activeLetter = null;

    public ?int $cityId = null;

    public ?float $userLat = null;
    public ?float $userLng = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'cityId' => ['except' => null],
        'filter' => ['except' => 'semua'],
        'sort' => ['except' => 'relevance'],
        'activeLetter' => ['except' => null],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCityId(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        if ($this->filter === 'terdekat') {
            $this->dispatch('request-location');
        }
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        // If sorting A-Z or Z-A, activeLetter might be relevant, 
        // but if switching back to relevance, maybe clear it? 
        // For now, keep them independent or just reset page.
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
        return City::select(['id', 'name'])->orderBy('name')->get();
    }

    public function render()
    {
        $query = Cafe::query()
            ->where('status', 'published')
            ->select([
                'id',
                'name',
                'city_id',
                'address',
                'latitude',
                'longitude',
                'image_path',
                'social_links',
                'opening_time',
                'closing_time'
            ])
            ->with(['facilities:id,cafe_id,name', 'city:id,name']);

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Letter Filter
        if ($this->activeLetter) {
            $query->where('name', 'like', $this->activeLetter . '%');
        }

        // Filter Logic
        if ($this->filter === 'buka') {
            $now = now()->format('H:i:s');
            $query->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereColumn('closing_time', '>', 'opening_time')
                        ->where('opening_time', '<=', $now)
                        ->where('closing_time', '>=', $now);
                })->orWhere(function ($sub) use ($now) {
                    $sub->whereColumn('closing_time', '<', 'opening_time')
                        ->where(function ($time) use ($now) {
                            $time->where('opening_time', '<=', $now)
                                ->orWhere('closing_time', '>=', $now);
                        });
                });
            });
        } elseif ($this->filter === 'terdekat' && $this->userLat && $this->userLng) {
            // "Terdekat" filter implies sorting by distance
            $query->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$this->userLat, $this->userLng, $this->userLat]
            )->orderBy('distance');
        }

        // Sort Logic (if not already sorted by distance via filter)
        if ($this->filter !== 'terdekat') {
            if ($this->sort === 'name_az') {
                $query->orderBy('name', 'asc');
            } elseif ($this->sort === 'name_za') {
                $query->orderBy('name', 'desc');
            } else {
                $query->latest();
            }
        }

        $cafesPaginator = $query->paginate(12);

        // Dispatch events for map update if needed
        $this->dispatch('cafes-updated', cafes: collect($cafesPaginator->items())->map(fn($c) => [
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
