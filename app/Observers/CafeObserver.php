<?php

namespace App\Observers;

use App\Models\Cafe;
use Illuminate\Support\Facades\Cache;

class CafeObserver
{
    /**
     * Handle the Cafe "created" event.
     */
    public function created(Cafe $cafe): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Cafe "updated" event.
     */
    public function updated(Cafe $cafe): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Cafe "deleted" event.
     */
    public function deleted(Cafe $cafe): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Cafe "restored" event.
     */
    public function restored(Cafe $cafe): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Cafe "force deleted" event.
     */
    public function forceDeleted(Cafe $cafe): void
    {
        $this->clearCache();
    }

    /**
     * Clear relevant caches.
     */
    private function clearCache(): void
    {
        Cache::forget('home_cafes');
    }
}
