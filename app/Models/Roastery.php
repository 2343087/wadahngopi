<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roastery extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'city_id',
        'description',
        'address',
        'google_maps_url',
        'whatsapp_number',
        'image_path',
        'images',
        'latitude',
        'longitude',
        'social_links',
        'owner_id',
        'status',
        'is_24_hours',
        'operating_hours',
        'weekday_open',
        'weekday_close',
        'weekend_open',
        'weekend_close',
    ];

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'social_links' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'operating_hours' => 'array',
            'is_24_hours' => 'boolean',
            'weekday_open' => 'string',
            'weekday_close' => 'string',
            'weekend_open' => 'string',
            'weekend_close' => 'string',
        ];
    }

    /**
     * Check if the roastery is currently open.
     */
    public function getIsOpenAttribute(): bool
    {
        // 1. 24 hours check
        if ($this->is_24_hours) {
            return true;
        }

        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);
        $prefix = $isWeekend ? 'weekend' : 'weekday';

        // 2. Check explicit columns (Optimized & Fast)
        $open = $this->{$prefix . '_open'};
        $close = $this->{$prefix . '_close'};

        if ($open && $close) {
            return $this->checkTimeInRange($open, $close);
        }

        // 3. Fallback: Check JSON operating_hours
        if (!empty($this->operating_hours)) {
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $this->checkTimeInRange($schedule['open'], $schedule['close']);
            }
        }

        return false;
    }

    /**
     * Get today's operating hours for display.
     *
     * @return array{open: string, close: string, label?: string}|null
     */
    public function getTodayHoursAttribute(): ?array
    {
        if ($this->is_24_hours) {
            return ['open' => '00:00', 'close' => '24:00', 'label' => '24 Jam'];
        }

        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);

        // Priority: JSON > Columns
        if (!empty($this->operating_hours)) {
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $schedule;
            }
        }

        // Fallback to columns
        $prefix = $isWeekend ? 'weekend' : 'weekday';
        $open = $this->{$prefix . '_open'};
        $close = $this->{$prefix . '_close'};

        if ($open && $close) {
            return ['open' => $open, 'close' => $close];
        }

        return null;
    }

    /**
     * Check if current time is within given range.
     */
    private function checkTimeInRange(string $open, string $close): bool
    {
        // Use generic service shared with Cafe logic
        return app(\App\Services\CafeSearchService::class)->isTimeInRange($open, $close);
    }

    /**
     * Scope a query to only include open roasteries.
     * Adapted from CafeSearchService but excludes legacy columns.
     */
    public function scopeOpenNow($query)
    {
        $now = now()->format('H:i:s');
        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);
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

    protected static function booted(): void
    {
        static::creating(function ($roastery) {
            if (empty($roastery->slug)) {
                $roastery->slug = static::generateUniqueSlug($roastery->name);
            }
        });

        static::saving(function ($roastery) {
            // Sync operating_hours JSON to dedicated columns for performance query scope
            if (!empty($roastery->operating_hours)) {
                $hours = $roastery->operating_hours;
                $roastery->weekday_open = $hours['weekday']['open'] ?? null;
                $roastery->weekday_close = $hours['weekday']['close'] ?? null;
                $roastery->weekend_open = $hours['weekend']['open'] ?? null;
                $roastery->weekend_close = $hours['weekend']['close'] ?? null;
            }
        });

        static::updating(function ($roastery) {
            if ($roastery->isDirty('name') && !$roastery->isDirty('slug')) {
                $roastery->slug = static::generateUniqueSlug($roastery->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
