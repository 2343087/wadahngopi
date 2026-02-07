<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CafeController extends Controller
{
    /**
     * Display a listing of the cafes.
     * Cached for 5 minutes to improve performance.
     */
    public function index(): View
    {
        $cafes = Cache::remember('home_cafes', now()->addMinutes(5), function () {
            return Cafe::where('status', 'published')
                ->with('facilities')
                ->latest()
                ->get();
        });

        return view('home', compact('cafes'));
    }

    /**
     * Display the specified cafe.
     */
    public function show(Cafe $cafe): View
    {
        abort_if($cafe->status !== 'published', 404);

        $cafe->load(['facilities']);

        return view('cafes.show', compact('cafe'));
    }
}
