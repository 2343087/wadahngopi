<?php

namespace App\Livewire;

use App\Models\Information;
use Illuminate\Support\Facades\Cache;
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
        $this->resetPage();
    }

    public function getPopularInformationsProperty()
    {
        // Cached for 5 minutes with selective columns
        return Cache::remember(
            'info_feed_popular',
            now()->addMinutes(5),
            fn() =>
            Information::where('is_published', true)
                ->select(['id', 'title', 'slug', 'image_path', 'published_at', 'views', 'category', 'created_at'])
                ->orderBy('views', 'desc')
                ->take(5)
                ->get()
        );
    }

    public function render()
    {
        $query = Information::where('is_published', true)
            ->select(['id', 'title', 'slug', 'image_path', 'published_at', 'views', 'category', 'created_at']);

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

