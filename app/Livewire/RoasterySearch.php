<?php

namespace App\Livewire;

use App\Models\Roastery;
use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class RoasterySearch extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'semua';
    public string $sort = 'relevance';
    public ?int $cityId = null;
    public ?float $userLat = null;
    public ?float $userLng = null;
    public ?string $activeLetter = null;

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

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
        if (in_array($sort, ['name_az', 'name_za'])) {
            $this->activeLetter = null;
        }
        $this->resetPage();
    }

    public function setLetter(?string $letter): void
    {
        if ($this->activeLetter === $letter) {
            $this->activeLetter = null;
        } else {
            $this->activeLetter = $letter;
            $this->sort = 'name_az';
        }
        $this->resetPage();
    }

    public function resetAllFilters(): void
    {
        $this->search = '';
        $this->filter = 'semua';
        $this->sort = 'relevance';
        $this->cityId = null;
        $this->userLat = null;
        $this->userLng = null;
        $this->activeLetter = null;
        $this->resetPage();
    }

    public function setUserLocation(float $lat, float $lng): void
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        if ($this->filter === 'terdekat') {
            $this->sort = 'distance';
        }
    }

    public function getCitiesProperty()
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'cities_list',
            now()->addMinutes(10),
            fn() =>
            City::select(['id', 'name'])->orderBy('name')->get()
        );
    }

    public function render()
    {
        $query = Roastery::query()
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
                'weekend_close'
            ])
            ->with(['city:id,name']);

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($this->activeLetter) {
            $query->where('name', 'like', $this->activeLetter . '%');
        }

        if ($this->filter === 'buka') {
            $query->openNow();
        } elseif ($this->filter === 'terdekat' && $this->userLat !== null && $this->userLng !== null) {
            $query->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$this->userLat, $this->userLng, $this->userLat]
            )->orderBy('distance');
        }

        if ($this->filter !== 'terdekat') {
            if ($this->sort === 'name_az') {
                $query->orderBy('name', 'asc');
            } elseif ($this->sort === 'name_za') {
                $query->orderBy('name', 'desc');
            } else {
                $query->latest();
            }
        }

        $roasteries = $query->paginate(12);

        return view('livewire.roastery-search', [
            'roasteries' => $roasteries,
            'cities' => $this->cities,
        ]);
    }
}
