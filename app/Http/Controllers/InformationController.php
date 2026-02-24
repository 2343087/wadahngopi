<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

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
        // Uses cache-based batching to avoid per-request DB writes
        $sessionKey = "info_viewed_{$information->id}";
        if (! session()->has($sessionKey)) {
            Cache::increment("info_views:{$information->id}");
            session()->put($sessionKey, true);
        }

        return view('information.show', compact('information'));
    }
}
