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
                                    ->where(fn($k) => $k->whereRaw("? >= $openCol", [$now])
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
     * Scope query to search using Fulltext Index with LIKE fallback.
     * Short terms (≤3 chars) use LIKE since fulltext min word length is typically 3-4.
     * If FULLTEXT index is missing (e.g. on hosting where migration didn't run),
     * gracefully falls back to LIKE-only search instead of crashing.
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        // Sanitize wildcards to prevent LIKE injection (% and _ are SQL wildcards)
        $safeTerm = str_replace(['%', '_'], ['\%', '\_'], $term);

        // For short terms, Fulltext might not trigger well, use LIKE
        if (strlen($term) <= 3) {
            $query->where(function ($q) use ($safeTerm) {
                $q->where('name', 'like', "%{$safeTerm}%")
                    ->orWhere('address', 'like', "%{$safeTerm}%");
            });

            return;
        }

        // Sanitize term to prevent SQL syntax errors from boolean operators
        $sanitizedTerm = preg_replace('/[+\-><()~*\"@]/', ' ', $term);
        $sanitizedTerm = trim(preg_replace('/\s+/', ' ', $sanitizedTerm));

        // Try Fulltext first, fallback to LIKE if index doesn't exist
        try {
            // Test if FULLTEXT index exists by building a test query
            $tableName = $query->getModel()->getTable();
            \Illuminate\Support\Facades\DB::select(
                "SELECT 1 FROM `{$tableName}` WHERE MATCH(`name`, `address`, `description`) AGAINST(? IN BOOLEAN MODE) LIMIT 1",
                [$sanitizedTerm]
            );

            // If we get here, FULLTEXT index exists — use it
            $query->where(function ($q) use ($safeTerm, $sanitizedTerm) {
                $q->whereFullText(['name', 'address', 'description'], $sanitizedTerm, ['mode' => 'boolean'])
                    ->orWhere('name', 'like', "%{$safeTerm}%");
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // FULLTEXT index doesn't exist — fallback to LIKE search
            $query->where(function ($q) use ($safeTerm) {
                $q->where('name', 'like', "%{$safeTerm}%")
                    ->orWhere('address', 'like', "%{$safeTerm}%")
                    ->orWhere('description', 'like', "%{$safeTerm}%");
            });
        }
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
            // Cross-day logic: Open 22:00, Close 02:00
            // Current time 01:00 is VALID (<= 02:00)
            // Current time 23:00 is VALID (>= 22:00)
            return $now >= $open || $now <= $close;
        }

        return $now >= $open && $now <= $close;
    }
}
