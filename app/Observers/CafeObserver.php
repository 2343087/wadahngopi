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
        $this->clearCache($cafe);
    }

    /**
     * Handle the Cafe "updated" event.
     */
    public function updated(Cafe $cafe): void
    {
        $this->clearCache($cafe);
    }

    /**
     * Handle the Cafe "deleted" event.
     */
    public function deleted(Cafe $cafe): void
    {
        $this->clearCache($cafe);
    }

    /**
     * Handle the Cafe "restored" event.
     */
    public function restored(Cafe $cafe): void
    {
        $this->clearCache($cafe);
    }

    /**
     * Handle the Cafe "force deleted" event.
     */
    public function forceDeleted(Cafe $cafe): void
    {
        $this->clearCache($cafe);
    }

    /**
     * Clear relevant caches.
     */
    private function clearCache(?Cafe $cafe = null): void
    {
        Cache::forget('home_cafes');

        if ($cafe) {
            Cache::forget("cafe_{$cafe->id}");
        }
    }
}
