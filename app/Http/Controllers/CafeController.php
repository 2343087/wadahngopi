<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
// Note: Cafe + Cache still used in show() method

class CafeController extends Controller
{
    /**
     * Display the home landing page.
     * Note: Home page is pure branding/CTA — no cafe data needed.
     */
    public function index(): View
    {
        return view('home');
    }

    /**
     * Display the specified cafe.
     * Cached per-cafe for 10 minutes.
     */
    public function show(Cafe $cafe): View
    {
        abort_if($cafe->status !== 'published', 404);

        // Cache individual cafe with relationships
        $cafe = Cache::remember("cafe_{$cafe->slug}", now()->addMinutes(10), function () use ($cafe) {
            $cafe->load(['facilities', 'city']);
            return $cafe;
        });

        return view('cafes.show', compact('cafe'));
    }
}
