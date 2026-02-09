<?php

namespace App\Livewire;

use App\Models\Cafe;
use App\Models\Roastery;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class SavedItems extends Component
{
    public array $cafeIds = [];
    public array $roasteryIds = [];

    public array $items = [];

    public function mount(array $cafeIds = [], array $roasteryIds = []): void
    {
        $this->updateIds($cafeIds, $roasteryIds);
    }

    public function updateIds(array $cafeIds = [], array $roasteryIds = []): void
    {
        $this->cafeIds = array_slice(array_filter(array_map('intval', $cafeIds)), 0, 50);
        $this->roasteryIds = array_slice(array_filter(array_map('intval', $roasteryIds)), 0, 50);

        $this->loadItems();
    }

    public function loadItems(): void
    {
        $this->items = [];

        // Load Cafes
        if (!empty($this->cafeIds)) {
            $cafes = Cafe::query()
                ->whereIn('id', $this->cafeIds)
                ->where('status', 'published')
                ->with('facilities')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'type' => 'cafe',
                    'name' => $c->name ?? 'Unnamed Cafe',
                    'address' => $c->address ?? '',
                    'isOpen' => (bool) $c->is_open,
                    'tags' => $c->facilities->pluck('name')->toArray(),
                    'image' => $c->image_path ? (str_starts_with($c->image_path, 'http') ? $c->image_path : Storage::url($c->image_path)) : null,
                    'url' => route('cafes.show', $c),
                    'timestamp' => 0 // We don't have this, so they will be grouped by type essentially if we sort
                ]);
            $this->items = array_merge($this->items, $cafes->toArray());
        }

        // Load Roasteries
        if (!empty($this->roasteryIds)) {
            $roasteries = Roastery::query()
                ->whereIn('id', $this->roasteryIds)
                ->select(['id', 'name', 'slug', 'address', 'image_path', 'is_24_hours', 'operating_hours', 'weekday_open', 'weekday_close', 'weekend_open', 'weekend_close'])
                ->get()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'type' => 'roastery',
                    'name' => $r->name,
                    'address' => $r->address ?? '',
                    'isOpen' => (bool) $r->is_open,
                    'tags' => ['Roastery', 'Beans'], // Default tags since roastery has no facilities
                    'image' => $r->image_path ? (str_starts_with($r->image_path, 'http') ? $r->image_path : Storage::url($r->image_path)) : null,
                    'url' => route('roastery.show', $r),
                ]);
            $this->items = array_merge($this->items, $roasteries->toArray());
        }
    }

    public function render()
    {
        return view('livewire.saved-items');
    }
}
