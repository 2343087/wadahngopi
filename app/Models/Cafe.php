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
    ];

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
        ];
    }
}
