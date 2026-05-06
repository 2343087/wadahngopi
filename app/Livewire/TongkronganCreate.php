<?php

namespace App\Livewire;

use App\Models\Cafe;
use Livewire\Component;

class TongkronganCreate extends Component
{
    public string $title = '';
    public string $search = '';
    public array $selectedCafes = [];
    public array $searchResults = [];

    protected $rules = [
        'title' => 'required|string|max:100',
    ];

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Cafe::where('status', 'published')
            ->where('name', 'like', "%{$this->search}%")
            ->whereNotIn('id', collect($this->selectedCafes)->pluck('id'))
            ->select(['id', 'name', 'address', 'slug', 'image_path'])
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function addCafe(int $cafeId)
    {
        if (count($this->selectedCafes) >= 5) {
            $this->dispatch('toast', message: 'Maksimal 5 cafe per list!', type: 'warning');
            return;
        }

        $cafe = Cafe::where('status', 'published')->select(['id', 'name', 'address', 'slug', 'image_path'])->find($cafeId);
        if ($cafe && !collect($this->selectedCafes)->contains('id', $cafeId)) {
            $this->selectedCafes[] = $cafe->toArray();
        }

        $this->search = '';
        $this->searchResults = [];
    }

    public function removeCafe(int $cafeId)
    {
        $this->selectedCafes = array_values(
            array_filter($this->selectedCafes, fn($c) => $c['id'] !== $cafeId)
        );
    }

    public function render()
    {
        return view('livewire.tongkrongan-create');
    }
}
