<?php

namespace App\Livewire;

use App\Models\Cafe;
use App\Services\CafeSearchService;
use Livewire\Component;

class CafeRoulette extends Component
{
    public bool $isOpen = false;
    public array $candidates = [];
    public ?array $winner = null;
    public bool $isSpinning = false;
    public int $lastSpinAt = 0;

    /**
     * Spin the roulette — fetch random open cafes.
     * Rate-limited: minimum 2 seconds between spins.
     */
    public function spin(): void
    {
        // Server-side rate limit: 2 second cooldown
        $now = now()->timestamp;
        if ($now - $this->lastSpinAt < 2) {
            return;
        }
        $this->lastSpinAt = $now;

        $searchService = app(CafeSearchService::class);

        // Fetch published cafes that are currently open
        $query = Cafe::query()
            ->where('status', 'published')
            ->with(['city', 'facilities']);

        // Apply openNow scope
        $query = $searchService->scopeOpenNow($query);

        // Get random batch of open cafes and shuffle for maximum randomness
        $cafes = $query->inRandomOrder()
            ->take(8)
            ->get()
            ->shuffle();

        if ($cafes->isEmpty()) {
            $this->candidates = [];
            $this->winner = null;
            return;
        }

        $this->candidates = $cafes->map(function (Cafe $cafe) {
            $images = $cafe->processed_images;
            $firstImage = count($images) > 0
                ? $images[0]
                : asset('wadahicon.png');

            return [
                'id' => $cafe->id,
                'name' => $cafe->name,
                'slug' => $cafe->slug,
                'image' => $firstImage,
                'address' => $cafe->address,
                'city' => $cafe->city?->name ?? '',
                'is_open' => $cafe->is_open,
                'url' => route('cafes.show', $cafe->slug),
            ];
        })->values()->toArray();

        // Winner = truly random pick from candidates
        $winnerIdx = random_int(0, count($this->candidates) - 1);
        $this->winner = $this->candidates[$winnerIdx];
    }

    public function openModal(): void
    {
        $this->isOpen = true;
        $this->candidates = [];
        $this->winner = null;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->candidates = [];
        $this->winner = null;
        $this->isSpinning = false;
    }

    public function render()
    {
        return view('livewire.cafe-roulette');
    }
}
