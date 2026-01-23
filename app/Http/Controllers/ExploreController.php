<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cafe::where('status', 'published');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filter by facilities (including any facility)
        if ($request->filled('facilities')) {
            $facilities = $request->input('facilities');
            if (! is_array($facilities)) {
                $facilities = explode(',', $facilities);
            }
            $query->whereHas('facilities', function ($q) use ($facilities) {
                $q->whereIn('facilities.id', $facilities);
            });
        }

        // Filter: Open Now
        if ($request->boolean('open_now')) {
            $now = now()->format('H:i:s');
            $query->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereColumn('closing_time', '>', 'opening_time')
                        ->where('opening_time', '<=', $now)
                        ->where('closing_time', '>=', $now);
                })->orWhere(function ($sub) use ($now) {
                    // Overnight (e.g. 18:00 to 02:00)
                    $sub->whereColumn('closing_time', '<', 'opening_time')
                        ->where(function ($time) use ($now) {
                            $time->where('opening_time', '<=', $now)
                                ->orWhere('closing_time', '>=', $now);
                        });
                });
            });
        }

        // Sorting
        if ($request->input('sort') === 'nearest' && $request->filled('lat') && $request->filled('lng')) {
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $query->select('*')
                ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
                ->orderBy('distance');
        } else {
            $query->latest();
        }

        $cafes = $query->with('facilities')->get();
        $allFacilities = \App\Models\Facility::all();

        return view('explore', compact('cafes', 'allFacilities'));
    }
}
