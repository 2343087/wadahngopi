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
        'source_name',
        'image_path',
        'source_url',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
