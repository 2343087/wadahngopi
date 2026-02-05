<?php

namespace App\Livewire;

use App\Models\Information;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class InformationFeed extends Component
{
    use WithPagination;

    public string $activeCategory = 'Semua';

    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
        $this->resetPage(); // Reset pagination when category changes
    }

    public function getPopularInformationsProperty()
    {
        // Always show top 3 popular regardless of category filter, 
        // OR filtering them? Usually 'Popular' is global, but let's make it global for now to keep the UI consistent.
        return Information::where('is_published', true)
            ->orderBy('views', 'desc')
            ->take(5) // Get 5 for the horizontal scroll
            ->get();
    }

    public function render()
    {
        $query = Information::where('is_published', true);

        if ($this->activeCategory !== 'Semua') {
            $query->where('category', $this->activeCategory);
        }

        $informations = $query->latest('published_at')
            ->paginate(10);

        return view('livewire.information-feed', [
            'informations' => $informations,
            'popularInformations' => $this->popularInformations,
        ]);
    }
}
