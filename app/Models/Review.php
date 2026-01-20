<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'user_name',
        'rating',
        'comment',
    ];

    /**
     * Get the cafe that owns the review.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Cafe, $this>
     */
    public function cafe()
    {
        return $this->belongsTo(Cafe::class);
    }
}
