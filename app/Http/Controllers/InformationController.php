<?php

namespace App\Http\Controllers;

class InformationController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // Berita Populer (Top 3 views)
        $popularInformations = \App\Models\Information::where('is_published', true)
            ->orderBy('views', 'desc')
            ->take(3)
            ->get();

        // Berita Terbaru (excludes popular if already shown, or just show all latest)
        $informations = \App\Models\Information::where('is_published', true)
            ->latest('published_at')
            ->get();

        return view('information.index', compact('informations', 'popularInformations'));
    }

    public function show(\App\Models\Information $information): \Illuminate\View\View
    {
        abort_unless($information->is_published, 404);

        // Increment views safely from hacker/bot spam by simple increment
        // For production we might want to use session-based or IP-based but simple increment is requested
        $information->increment('views');

        return view('information.show', compact('information'));
    }
}
