<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'name',
        'price',
        'type',
        'image_path',
    ];

    /**
     * Get the cafe that owns the menu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Cafe, $this>
     */
    public function cafe()
    {
        return $this->belongsTo(Cafe::class);
    }
}
