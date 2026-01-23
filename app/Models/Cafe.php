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
        'description',
        'address',
        'google_maps_url',
        'whatsapp_number',
        'has_wifi',
        'rating',
        'image_path',
        'images', // Multiple images (JSON array)
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'owner_id',
        'status',
        'total_energy',
    ];

    protected $casts = [
        'images' => 'array', // Cast JSON to array
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the menus for the cafe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Menu, $this>
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
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

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function getIsOpenAttribute(): bool
    {
        if (! $this->opening_time || ! $this->closing_time) {
            return false;
        }

        $now = now()->setTimezone('Asia/Pontianak')->format('H:i:s'); // Assuming Pontianak/WITA or WIB. Defaulting to App timezone, maybe safe to use 'Asia/Jakarta' or config value. Sticking to simple now() for generic context or explicit if needed.
        // Let's use simple now() which uses App config timezone.
        $now = now()->format('H:i:s');

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
            'has_wifi' => 'boolean',
            'rating' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }
}
