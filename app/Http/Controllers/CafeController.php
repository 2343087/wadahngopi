<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\View\View;

class CafeController extends Controller
{
    /**
     * Display a listing of the cafes.
     */
    public function index(): View
    {
        $cafes = Cafe::where('status', 'published')->with('facilities')->latest()->get();

        return view('home', compact('cafes'));
    }

    /**
     * Display the specified cafe.
     */
    public function show(Cafe $cafe): View
    {
        abort_if($cafe->status !== 'published', 404);

        $cafe->load(['menus', 'reviews', 'facilities']);

        return view('cafes.show', compact('cafe'));
    }
}
