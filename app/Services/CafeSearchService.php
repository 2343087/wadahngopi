<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class CafeSearchService
{
    public function isWeekend(): bool
    {
        return in_array(now()->dayOfWeek, [0, 6]);
    }

    /**
     * Scope query to find cafes that are currently open.
     */
    public function scopeOpenNow(Builder $query): Builder
    {
        $now = now()->format('H:i:s');
        $isWeekend = $this->isWeekend();

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
                                        ->where(fn ($k) => $k->whereRaw("? >= $openCol", [$now])
                                            ->orWhereRaw("? <= $closeCol", [$now]));
                                });
                        });
                });
        });
    }

    /**
     * Scope query to order by nearest location using Bounding Box optimization.
     * 1. First filters by rough bounding box (using index)
     * 2. Then calculates exact distance
     */
    public function scopeNearest(Builder $query, float $lat, float $lng, int $radiusKm = 20): Builder
    {
        // Use MySQL Spatial Function ST_Distance_Sphere for ultra-fast calculation
        // Default SRID 4326 in MySQL 8.0+ is (Latitude, Longitude)
        $point = "POINT($lat $lng)";

        return $query
            ->whereRaw('ST_Distance_Sphere(location, ST_GeomFromText(?, 4326)) <= ?', [$point, $radiusKm * 1000])
            ->selectRaw('(ST_Distance_Sphere(location, ST_GeomFromText(?, 4326)) / 1000) AS distance', [$point])
            ->orderBy('distance');
    }

    /**
     * Scope query to search using Fulltext Index.
     * Fallback to LIKE if search term is short or no results.
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        // For short terms, Fulltext might not trigger well, use LIKE
        if (strlen($term) <= 3) {
            $query->where('name', 'like', "%{$term}%");

            return;
        }

        // Fulltext Search in Boolean Mode
        // Sanitize term to prevent SQL syntax errors from boolean operators (<, >, (, ), etc)
        $sanitizedTerm = preg_replace('/[+\-><()~*\"@]/', ' ', $term);
        $sanitizedTerm = trim(preg_replace('/\s+/', ' ', $sanitizedTerm));

        $query->where(function ($q) use ($term, $sanitizedTerm) {
            $q->whereFullText(['name', 'address', 'description'], $sanitizedTerm, ['mode' => 'boolean'])
                ->orWhere('name', 'like', "%{$term}%"); // Hybrid approach: Prefer FT, fallback/combine LIKE for partial matches not covered by FT
        });

        // Note: In pure boolean mode we might not need "like", but for user expectations (partial words), hybrid is safer initially.
        // Optimization: If dataset is huge, remove the OR LIKE part.
    }

    /**
     * Check if a specific time is within range.
     * Handles overnight hours.
     */
    public function isTimeInRange(string $open, string $close, ?string $currentTime = null): bool
    {
        $now = $currentTime ?: now()->format('H:i:s');

        // Normalize to H:i:s format
        $open = strlen($open) === 5 ? $open.':00' : $open;
        $close = strlen($close) === 5 ? $close.':00' : $close;

        if ($close < $open) {
            // Cross-day logic: Open 22:00, Close 02:00
            // Current time 01:00 is VALID (<= 02:00)
            // Current time 23:00 is VALID (>= 22:00)
            return $now >= $open || $now <= $close;
        }

        return $now >= $open && $now <= $close;
    }
}
