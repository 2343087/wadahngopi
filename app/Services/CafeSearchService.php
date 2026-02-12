<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CafeSearchService
{
    /**
     * Scope query to find cafes that are currently open.
     */
    public function scopeOpenNow(Builder $query): Builder
    {
        $now = now()->format('H:i:s');
        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);

        // Use the new virtual indexed columns:
        // weekday_open, weekday_close OR weekend_open, weekend_close
        $prefix = $isWeekend ? 'weekend' : 'weekday';
        $openCol = "{$prefix}_open";
        $closeCol = "{$prefix}_close";

        return $query->where(function ($q) use ($now, $openCol, $closeCol) {
            // 1. Always open
            $q->where('is_24_hours', true)

                // 2. Optimized Virtual Column Query (Indexed)
                ->orWhere(function ($sub) use ($now, $openCol, $closeCol) {
                    $sub->whereNotNull($openCol)
                        ->whereNotNull($closeCol)
                        ->where(function ($time) use ($now, $openCol, $closeCol) {
                            // Normal case: 08:00 - 22:00 (Open < Close)
                            $time->whereRaw("$closeCol > $openCol")
                                ->whereRaw("? BETWEEN $openCol AND $closeCol", [$now])

                                // Overnight case: 22:00 - 02:00 (Close < Open)
                                ->orWhere(function ($overnight) use ($now, $openCol, $closeCol) {
                                $overnight->whereRaw("$closeCol < $openCol")
                                    ->where(fn($k) => $k->whereRaw("? >= $openCol", [$now])
                                        ->orWhereRaw("? <= $closeCol", [$now]));
                            });
                        });
                });
        });
    }

    /**
     * Check if a specific time is within range.
     * Handles overnight hours.
     */
    public function isTimeInRange(string $open, string $close, ?string $currentTime = null): bool
    {
        $now = $currentTime ?: now()->format('H:i:s');

        // Normalize to H:i:s format
        $open = strlen($open) === 5 ? $open . ':00' : $open;
        $close = strlen($close) === 5 ? $close . ':00' : $close;

        if ($close < $open) {
            return $now >= $open || $now <= $close;
        }

        return $now >= $open && $now <= $close;
    }
}
