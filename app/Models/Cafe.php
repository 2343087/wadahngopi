<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    /** @use HasFactory<\Database\Factories\CafeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city_id',
        'description',
        'address',
        'google_maps_url',
        'whatsapp_number',
        'has_wifi',
        'image_path',
        'images',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'is_24_hours',
        'operating_hours',
        'owner_id',
        'status',
        'social_links',
        'menu_images',
    ];

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }


    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Check if the cafe is currently open.
     * Supports: 24-hour cafes, weekday/weekend schedules, legacy single time fields.
     */
    public function getIsOpenAttribute(): bool
    {
        // 1. Cafe 24 jam = selalu buka
        if ($this->is_24_hours) {
            return true;
        }

        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);
        $prefix = $isWeekend ? 'weekend' : 'weekday';

        // 2. Cek Virtual Columns (Optimized & Fast)
        // Attribute access handles generic access if loaded
        $open = $this->{$prefix . '_open'};
        $close = $this->{$prefix . '_close'};

        if ($open && $close) {
            return $this->checkTimeInRange($open, $close);
        }

        // 3. Fallback: Cek dari operating_hours (JSON) - if virtual cols hidden/null
        if (!empty($this->operating_hours)) {
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $this->checkTimeInRange($schedule['open'], $schedule['close']);
            }
        }

        // 4. Fallback ke field lama (backward compatibility)
        if (!$this->opening_time || !$this->closing_time) {
            return false;
        }

        return $this->checkTimeInRange($this->opening_time, $this->closing_time);
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

        if (!empty($this->operating_hours)) {
            $isWeekend = in_array(now()->dayOfWeek, [0, 6]);
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $schedule;
            }
        }

        if ($this->opening_time && $this->closing_time) {
            return [
                'open' => $this->opening_time,
                'close' => $this->closing_time,
            ];
        }

        return null;
    }

    /**
     * Check if current time is within given range.
     * Handles overnight hours (e.g., 18:00 - 02:00).
     */
    private function checkTimeInRange(string $open, string $close): bool
    {
        return app(\App\Services\CafeSearchService::class)->isTimeInRange($open, $close);
    }

    /**
     * Scope a query to only include open cafes.
     */
    public function scopeOpenNow($query): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Services\CafeSearchService::class)->scopeOpenNow($query);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'menu_images' => 'array',
            'social_links' => 'array',
            'operating_hours' => 'array',
            'has_wifi' => 'boolean',
            'is_24_hours' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'weekday_open' => 'string',
            'weekday_close' => 'string',
            'weekend_open' => 'string',
            'weekend_close' => 'string',
        ];
    }
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($cafe) {
            if (empty($cafe->slug)) {
                $cafe->slug = static::generateUniqueSlug($cafe->name);
            }
        });

        static::updating(function ($cafe) {
            if ($cafe->isDirty('name') && !$cafe->isDirty('slug')) {
                $cafe->slug = static::generateUniqueSlug($cafe->name);
            }
        });
    }

    /**
     * Generate a unique slug for the cafe.
     */
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

    /**
     * Mutator: Automatically format WhatsApp number to 62xxx
     * Ensures wa.me links work correctly.
     */
    public function setWhatsappNumberAttribute($value)
    {
        // 1. Remove non-numeric characters (spaces, dashes, etc.)
        $clean = preg_replace('/[^0-9]/', '', $value);

        // 2. Normalize 08xxx -> 628xxx
        if (str_starts_with($clean, '08')) {
            $clean = '62' . substr($clean, 1);
        }

        // 3. Normalize +62 -> 62 (already handled by numeric regex, but just in case)

        $this->attributes['whatsapp_number'] = $clean;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
