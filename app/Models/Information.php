<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    /** @use HasFactory<\Database\Factories\InformationFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'category',
        'image_path',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
