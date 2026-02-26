<?php

namespace App\Observers;

use App\Models\Roastery;
use Illuminate\Support\Facades\Cache;

class RoasteryObserver
{
    public function created(Roastery $roastery): void
    {
        $this->clearCache($roastery);
    }

    public function updated(Roastery $roastery): void
    {
        $this->clearCache($roastery);
    }

    public function deleted(Roastery $roastery): void
    {
        $this->clearCache($roastery);
    }

    public function restored(Roastery $roastery): void
    {
        $this->clearCache($roastery);
    }

    public function forceDeleted(Roastery $roastery): void
    {
        $this->clearCache($roastery);
    }

    /**
     * Clear relevant caches when roastery data changes.
     */
    private function clearCache(?Roastery $roastery = null): void
    {
        Cache::forget('cities_list');

        if ($roastery) {
            Cache::forget("roastery_{$roastery->slug}");
        }
    }
}
