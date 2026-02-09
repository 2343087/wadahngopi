<?php

namespace App\Livewire;

use App\Models\Roastery;
use Livewire\Component;

class RoasteryDetail extends Component
{
    public $roasteryId;
    public $isOpen = false;
    public $hasRoastery = false;

    public function mount($roasteryId)
    {
        $this->roasteryId = $roasteryId;
        $this->checkStatus();
    }

    public function checkStatus()
    {
        $roastery = Roastery::find($this->roasteryId);
        if ($roastery) {
            $this->hasRoastery = true;
            // Force re-evaluation of the accessor
            $this->isOpen = $roastery->is_open;
        }
    }

    public function refreshStatus()
    {
        $this->checkStatus();
    }

    public function render()
    {
        return view('livewire.roastery-detail');
    }
}
