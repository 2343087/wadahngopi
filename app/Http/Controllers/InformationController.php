<?php

namespace App\Http\Controllers;

class InformationController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $informations = \App\Models\Information::where('is_published', true)
            ->latest('published_at')
            ->get();

        return view('information.index', compact('informations'));
    }

    public function show(\App\Models\Information $information): \Illuminate\View\View
    {
        abort_unless($information->is_published, 404);

        return view('information.show', compact('information'));
    }
}
