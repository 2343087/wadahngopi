<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Support\Facades\Cache;

class InformationController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // Berita Populer (Top 3 views) - Cached 5 minutes
        $popularInformations = Cache::remember(
            'info_popular',
            now()->addMinutes(5),
            fn() =>
            Information::where('is_published', true)
                ->select(['id', 'title', 'slug', 'image_path', 'published_at', 'views'])
                ->orderBy('views', 'desc')
                ->take(3)
                ->get()
        );

        // Berita Terbaru - Cached 5 minutes
        $informations = Cache::remember(
            'info_latest',
            now()->addMinutes(5),
            fn() =>
            Information::where('is_published', true)
                ->select(['id', 'title', 'slug', 'image_path', 'published_at', 'views'])
                ->latest('published_at')
                ->get()
        );

        return view('information.index', compact('informations', 'popularInformations'));
    }

    public function show(\App\Models\Information $information): \Illuminate\View\View
    {
        abort_unless($information->is_published, 404);

        // Direct DB increment to ensure immediate real-time updates for the user.
        // "Anti-ngebug" - simple and reliable.
        $information->increment('views');

        return view('information.show', compact('information'));
    }
}
