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
     */
    public function getIsOpenAttribute(): bool
    {
        if (!$this->opening_time || !$this->closing_time) {
            return false;
        }

        $now = now()->format('H:i:s');

        // Handle overnight hours (e.g., 18:00 - 02:00)
        if ($this->closing_time < $this->opening_time) {
            return $now >= $this->opening_time || $now <= $this->closing_time;
        }

        return $now >= $this->opening_time && $now <= $this->closing_time;
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
            'has_wifi' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }
}
