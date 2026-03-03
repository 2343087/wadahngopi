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
        Cache::forget('active_roastery_ids');

        // Clear shuffled and total count caches via Redis pattern
        try {
            $prefix = config('cache.prefix', 'laravel_cache') . ':';

            foreach (['shuffled_roastery_*', 'roastery_total_*'] as $pattern) {
                $keys = \Illuminate\Support\Facades\Redis::keys($prefix . $pattern);
                foreach ($keys as $key) {
                    \Illuminate\Support\Facades\Redis::del($key);
                }
            }
        } catch (\Throwable $e) {
            // Fallback: cache expires naturally (5-30 min)
        }

        if ($roastery) {
            Cache::forget("roastery_{$roastery->slug}");
        }
    }
}
