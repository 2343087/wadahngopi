<?php

namespace App\Livewire;

use App\Models\Tongkrongan;
use Livewire\Component;

class TongkronganView extends Component
{
    public Tongkrongan $tongkrongan;

    public function mount(Tongkrongan $tongkrongan)
    {
        $this->tongkrongan = $tongkrongan;
    }

    public function refreshVotes()
    {
        $this->tongkrongan->load(['items.cafe', 'items.votes']);
    }

    public function render()
    {
        $this->tongkrongan->load([
            'items.cafe' => fn($q) => $q->select(['id', 'name', 'slug', 'address', 'image_path', 'is_24_hours', 'operating_hours', 'weekday_open', 'weekday_close', 'weekend_open', 'weekend_close']),
            'items.votes',
        ]);

        $items = $this->tongkrongan->items->sortByDesc(fn($item) => $item->votes->count())->values();
        $maxVotes = $items->max(fn($item) => $item->votes->count());

        return view('livewire.tongkrongan-view', [
            'items' => $items,
            'maxVotes' => $maxVotes,
            'isExpired' => $this->tongkrongan->is_expired,
            'expiresIn' => $this->tongkrongan->expires_at->diffForHumans(),
        ]);
    }
}
