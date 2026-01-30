<?php

namespace App\Livewire;

use App\Models\Cafe;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class SavedCafes extends Component
{
    public array $cafeIds = [];

    public array $cafes = [];

    public function mount(array $ids = []): void
    {
        $this->cafeIds = array_slice(
            array_filter(array_map('intval', $ids)),
            0,
            50
        );
        $this->loadCafes();
    }

    public function updateIds(array $ids): void
    {
        $this->cafeIds = array_slice(
            array_filter(array_map('intval', $ids)),
            0,
            50
        );
        $this->loadCafes();
    }

    public function loadCafes(): void
    {
        if (empty($this->cafeIds)) {
            $this->cafes = [];

            return;
        }

        $this->cafes = Cafe::query()
            ->whereIn('id', $this->cafeIds)
            ->where('status', 'published')
            ->with('facilities')
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
    }

    public function render()
    {
        return view('livewire.saved-cafes');
    }
}
