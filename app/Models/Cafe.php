<?php

namespace App\Models;

use App\Traits\HasOperatingHours;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cafe extends Model
{
    /** @use HasFactory<\Database\Factories\CafeFactory> */
    use HasFactory, HasOperatingHours;

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

    /**
     * Get the city that the cafe belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the owner of the cafe.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The facilities that belong to the cafe.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
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

        static::saving(function ($cafe) {
            // Sync operating_hours JSON to dedicated columns for performance query scope
            if (!empty($cafe->operating_hours) && $cafe->isDirty('operating_hours')) {
                $hours = $cafe->operating_hours;
                $cafe->weekday_open = $hours['weekday']['open'] ?? null;
                $cafe->weekday_close = $hours['weekday']['close'] ?? null;
                $cafe->weekend_open = $hours['weekend']['open'] ?? null;
                $cafe->weekend_close = $hours['weekend']['close'] ?? null;
            }
        });

        static::saved(function ($cafe) {
            // Invalidate cache when cafe is updated
            \Illuminate\Support\Facades\Cache::forget("cafe_{$cafe->slug}");
        });

        static::updating(function ($cafe) {
            if ($cafe->isDirty('name') && !$cafe->isDirty('slug')) {
                $cafe->slug = static::generateUniqueSlug($cafe->name);
            }
        });
    }

    /**
     * Mutator: Automatically format WhatsApp number to 62xxx
     * Ensures wa.me links work correctly.
     */
    public function setWhatsappNumberAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['whatsapp_number'] = null;
            return;
        }

        // Remove non-numeric characters (spaces, dashes, etc.)
        $clean = preg_replace('/[^0-9]/', '', $value);

        // Normalize 08xxx -> 628xxx
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (!str_starts_with($clean, '62')) {
            $clean = '62' . $clean;
        }

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
