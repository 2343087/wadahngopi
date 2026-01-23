<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Reaction;
use Illuminate\Http\Request;
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
        $cafe->load(['menus', 'reviews', 'facilities']);

        return view('cafes.show', compact('cafe'));
    }

    /**
     * Store energy reaction for the cafe.
     */
    public function storeEnergy(Request $request, Cafe $cafe)
    {
        $validated = $request->validate([
            'energy_count' => 'required|integer|min:1|max:1000',
            'visitor_id' => 'required|string|max:255',
        ]);

        // Find or create reaction for this visitor
        $reaction = $cafe->reactions()->firstOrCreate(
            ['visitor_id' => $validated['visitor_id']]
        );

        $reaction->increment('energy_count', $validated['energy_count']);

        // Update total energy on cafe (caching for speed)
        $cafe->increment('total_energy', $validated['energy_count']);

        return response()->json([
            'success' => true,
            'new_total' => $cafe->total_energy,
            'message' => 'Energi terikirim! 🔥',
        ]);
    }
}
