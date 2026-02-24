<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Roastery;
use Livewire\Attributes\Lazy;
use Livewire\Component;

use Livewire\WithPagination;

#[Lazy]
class RoasterySearch extends Component
{
    use WithPagination;

    public int $perPage = 12;

    public string $search = '';

    public string $filter = 'semua';

    public string $sort = 'relevance';

    public ?int $cityId = null;

    public ?float $userLat = null;

    public ?float $userLng = null;

    public ?int $randomSeed = null;

    public ?string $activeLetter = null;

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

    public function setSort(string $sort): void
    {
        if (!in_array($sort, ['relevance', 'name_az', 'name_za', 'distance'])) {
            $sort = 'relevance';
        }

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
        $this->cityId = null;
        $this->userLat = null;
        $this->userLng = null;
        $this->activeLetter = null;
        $this->perPage = 12;
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
            now()->addHour(),
            fn() => City::select(['id', 'name'])->orderBy('name')->get()
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
                'weekend_close',
            ])
            ->with(['city:id,name']);

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

        if ($this->filter !== 'terdekat') {
            if ($this->sort === 'name_az') {
                $query->orderBy('name', 'asc');
            } elseif ($this->sort === 'name_za') {
                $query->orderBy('name', 'desc');
            } elseif (!$this->search && !$this->activeLetter && $this->sort === 'relevance') {
                // Fair Play: Cached random order (refreshes every 5 minutes)
                $randomIds = \Illuminate\Support\Facades\Cache::remember(
                    'roastery_random_order_' . ($this->randomSeed % 10),
                    now()->addMinutes(5),
                    fn() => Roastery::where('status', 'published')->pluck('id')->shuffle()->toArray()
                );

                if (!empty($randomIds)) {
                    $idList = implode(',', array_map('intval', $randomIds));
                    $query->orderByRaw("FIELD(id, {$idList})");
                }
            } else {
                $query->latest();
            }
        }

        $roasteries = $query->paginate($this->perPage);

        return view('livewire.roastery-search', [
            'roasteries' => $roasteries,
            'cities' => $this->cities,
        ]);
    }
}
