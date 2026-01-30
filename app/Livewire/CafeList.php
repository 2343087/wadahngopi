<?php

namespace App\Livewire;

use App\Models\Cafe;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CafeList extends Component
{
    public array $cafes = [];

    public function mount(): void
    {
        $this->loadCafes();
    }

    public function loadCafes(): void
    {
        $this->cafes = Cache::remember('home_cafes', now()->addMinutes(1), function () {
            return Cafe::query()
                ->where('status', 'published')
                ->with('facilities')
                ->latest()
                ->take(10)
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name ?? 'Unnamed Cafe',
                    'address' => $c->address ?? '',
                    'isOpen' => (bool) $c->is_open,
                    'facilities' => $c->facilities->pluck('name')->toArray(),
                    'image' => $c->image_path ? (str_starts_with($c->image_path, 'http') ? $c->image_path : Storage::url($c->image_path)) : null,
                    'url' => route('cafes.show', $c),
                ])
                ->toArray();
        });
    }

    public function render()
    {
        return view('livewire.cafe-list');
    }
}
