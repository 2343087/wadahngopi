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

    /**
     * Get the reviews for the cafe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Review, $this>
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
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

        // 2. Cek dari operating_hours (struktur baru)
        if (!empty($this->operating_hours)) {
            $dayOfWeek = now()->dayOfWeek; // 0=Minggu, 6=Sabtu
            $isWeekend = in_array($dayOfWeek, [0, 6]);
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $this->checkTimeInRange($schedule['open'], $schedule['close']);
            }
        }

        // 3. Fallback ke field lama (backward compatibility)
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
        $now = now()->format('H:i:s');

        // Normalize to H:i:s format
        $open = strlen($open) === 5 ? $open . ':00' : $open;
        $close = strlen($close) === 5 ? $close . ':00' : $close;

        // Handle overnight (e.g., 18:00 - 02:00)
        if ($close < $open) {
            return $now >= $open || $now <= $close;
        }

        return $now >= $open && $now <= $close;
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
        ];
    }
}
