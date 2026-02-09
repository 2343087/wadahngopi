<?php

namespace App\Traits;

use App\Services\CafeSearchService;

/**
 * Shared trait for models with operating hours logic.
 * Used by Cafe and Roastery models to avoid code duplication.
 */
trait HasOperatingHours
{
    /**
     * Check if the entity is currently open.
     * Supports: 24-hour, weekday/weekend schedules, legacy single time fields.
     */
    public function getIsOpenAttribute(): bool
    {
        if ($this->is_24_hours) {
            return true;
        }

        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);
        $prefix = $isWeekend ? 'weekend' : 'weekday';

        $open = $this->{$prefix . '_open'};
        $close = $this->{$prefix . '_close'};

        if ($open && $close) {
            return $this->checkTimeInRange($open, $close);
        }

        if (!empty($this->operating_hours)) {
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $this->checkTimeInRange($schedule['open'], $schedule['close']);
            }
        }

        // Legacy fallback for Cafe model
        if (property_exists($this, 'opening_time') || isset($this->attributes['opening_time'])) {
            if (!$this->opening_time || !$this->closing_time) {
                return false;
            }
            return $this->checkTimeInRange($this->opening_time, $this->closing_time);
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

        if (!empty($this->operating_hours)) {
            $schedule = $isWeekend
                ? ($this->operating_hours['weekend'] ?? null)
                : ($this->operating_hours['weekday'] ?? null);

            if ($schedule && !empty($schedule['open']) && !empty($schedule['close'])) {
                return $schedule;
            }
        }

        $prefix = $isWeekend ? 'weekend' : 'weekday';
        $open = $this->{$prefix . '_open'};
        $close = $this->{$prefix . '_close'};

        if ($open && $close) {
            return ['open' => $open, 'close' => $close];
        }

        // Legacy fallback
        if (isset($this->opening_time) && isset($this->closing_time)) {
            return ['open' => $this->opening_time, 'close' => $this->closing_time];
        }

        return null;
    }

    /**
     * Check if current time is within given range.
     */
    private function checkTimeInRange(string $open, string $close): bool
    {
        return app(CafeSearchService::class)->isTimeInRange($open, $close);
    }

    /**
     * Generate a unique slug for the model.
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
}
