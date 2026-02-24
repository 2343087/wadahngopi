<?php

namespace App\Http\Controllers;

class InformationController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // Livewire component handles data fetching.
        // Controller just returns the view wrapper.
        return view('information.index');
    }

    public function show(\App\Models\Information $information): \Illuminate\View\View
    {
        abort_unless($information->is_published, 404);

        // Rate-limited view counter: 1 increment per article per session
        // Prevents bot/crawler view inflation abuse
        $sessionKey = "info_viewed_{$information->id}";
        if (! session()->has($sessionKey)) {
            $information->increment('views');
            session()->put($sessionKey, true);
        }

        return view('information.show', compact('information'));
    }
}
