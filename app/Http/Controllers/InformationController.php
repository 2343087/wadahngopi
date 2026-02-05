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

        // Direct DB increment to ensure immediate real-time updates for the user.
        // "Anti-ngebug" - simple and reliable.
        $information->increment('views');

        return view('information.show', compact('information'));
    }
}
