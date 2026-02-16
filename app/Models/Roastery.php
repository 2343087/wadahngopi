<?php

namespace App\Models;

use App\Traits\HasOperatingHours;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Roastery extends Model
{
    use HasOperatingHours;

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
        'menu_images',
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

    /**
     * Get the city that the roastery belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the owner of the roastery.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Scope query to find roasteries that are currently open.
     */
    public function scopeOpenNow($query): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Services\CafeSearchService::class)->scopeOpenNow($query);
    }

    /**
     * Scope a query to only include roasteries near a location.
     */
    public function scopeNearest($query, $lat, $lng): \Illuminate\Database\Eloquent\Builder
    {
        return app(\App\Services\CafeSearchService::class)->scopeNearest($query, $lat, $lng);
    }

    /**
     * Scope a query to search roasteries by term.
     */
    public function scopeSearch($query, $term): void
    {
        app(\App\Services\CafeSearchService::class)->scopeSearch($query, $term);
    }

    /**
     * Set the whatsapp_number attribute.
     */
    public function setWhatsappNumberAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['whatsapp_number'] = null;
            return;
        }

        // Remove non-numeric characters
        $value = preg_replace('/[^0-9]/', '', $value);

        // Normalize Indonesian numbers
        if (str_starts_with($value, '0')) {
            $value = '62' . substr($value, 1);
        } elseif (!str_starts_with($value, '62')) {
            $value = '62' . $value;
        }

        $this->attributes['whatsapp_number'] = $value;
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'menu_images' => 'array',
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
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($roastery) {
            if (empty($roastery->slug)) {
                $roastery->slug = static::generateUniqueSlug($roastery->name);
            }
        });

        static::saving(function ($roastery) {
            // Sync operating_hours JSON to dedicated columns for performance query scope
            if (!empty($roastery->operating_hours) && $roastery->isDirty('operating_hours')) {
                $hours = $roastery->operating_hours;
                $roastery->weekday_open = data_get($hours, 'weekday.open');
                $roastery->weekday_close = data_get($hours, 'weekday.close');
                $roastery->weekend_open = data_get($hours, 'weekend.open');
                $roastery->weekend_close = data_get($hours, 'weekend.close');
            }
        });

        static::saved(function ($roastery) {
            // Invalidate cache
            \Illuminate\Support\Facades\Cache::forget("roastery_{$roastery->slug}");
        });

        static::updating(function ($roastery) {
            if ($roastery->isDirty('name') && !$roastery->isDirty('slug')) {
                $roastery->slug = static::generateUniqueSlug($roastery->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessor: Clean and process ambience images.
     */
    public function getProcessedImagesAttribute(): array
    {
        return $this->processImagesList(collect([$this->image_path])->merge($this->images ?? [])->all());
    }

    /**
     * Accessor: Clean and process menu images.
     */
    public function getProcessedMenuImagesAttribute(): array
    {
        return $this->processImagesList($this->menu_images ?? []);
    }

    /**
     * Helper to clean image paths.
     */
    protected function processImagesList(array $list): array
    {
        return collect($list)
            ->filter()
            ->map(function ($img) {
                if (empty($img))
                    return null;
                $cleanImg = preg_replace('/[\x00-\x1F\x7F\xA0\s]+/', '', $img);
                if (str_starts_with($cleanImg, 'http'))
                    return $cleanImg;
                return '/storage/' . $cleanImg;
            })
            ->filter()
            ->values()
            ->all() ?: ['https://images.unsplash.com/photo-1559056199-641a0ac8b55e?auto=format&fit=crop&q=80&w=1200'];
    }
}
