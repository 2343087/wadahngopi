<?php

namespace App\Livewire;

use App\Models\Cafe;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ExploreSearch extends Component
{
    public string $search = '';

    public string $filter = 'semua';

    public array $cafes = [];

    public array $cities = [];

    public ?int $cityId = null;

    public function mount(): void
    {
        $this->cities = \App\Models\City::orderBy('name')->get()->toArray();
        $this->loadCafes();
    }

    public function updatedSearch(): void
    {
        $this->loadCafes();
        $this->dispatch('cafes-updated', cafes: $this->cafes);
    }

    public function updatedCityId(): void
    {
        $this->loadCafes();
        $this->dispatch('cafes-updated', cafes: $this->cafes);
    }

    public function updatedFilter(): void
    {
        $this->loadCafes();
        $this->dispatch('cafes-updated', cafes: $this->cafes);
    }

    public function loadCafes(): void
    {
        $query = Cafe::query()
            ->where('status', 'published')
            ->with(['facilities', 'city']);

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);
            $query->where('name', 'like', '%' . $search . '%');
        }

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
        }

        $this->cafes = $query->latest()->get()->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name ?? 'Unnamed Cafe',
            'city' => $c->city?->name ?? 'Lokasi',
            'address' => $c->address ?? '',
            'isOpen' => (bool) $c->is_open,
            'lat' => $c->latitude,
            'lng' => $c->longitude,
            'facilities' => $c->facilities->pluck('name')->toArray(),
            'socialLinks' => collect($c->social_links ?? [])
                ->filter(fn($s) => filter_var($s['show'] ?? false, FILTER_VALIDATE_BOOLEAN) && !empty($s['url']))
                ->map(fn($s) => ['platform' => $s['platform'], 'url' => $s['url']])
                ->values()
                ->toArray(),
            'image' => $c->image_path ? (str_starts_with($c->image_path, 'http') ? $c->image_path : Storage::url($c->image_path)) : null,
            'url' => route('cafes.show', $c),
        ])->values()->toArray();
    }

    public function render()
    {
        return view('livewire.explore-search');
    }
}
