<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExploreFilterRequest;
use App\Models\Cafe;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(ExploreFilterRequest $request): View
    {
        $validated = $request->validated();
        $query = Cafe::where('status', 'published');

        // Search with escaped wildcards for security
        if (! empty($validated['search'])) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $validated['search']);
            $query->where('name', 'like', '%'.$search.'%');
        }

        // Filter by facilities
        if (! empty($validated['facilities'])) {
            $facilities = $validated['facilities'];
            $query->whereHas('facilities', function ($q) use ($facilities) {
                $q->whereIn('facilities.id', $facilities);
            });
        }

        // Filter: Open Now
        if (! empty($validated['open_now'])) {
            $now = now()->format('H:i:s');
            $query->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereColumn('closing_time', '>', 'opening_time')
                        ->where('opening_time', '<=', $now)
                        ->where('closing_time', '>=', $now);
                })->orWhere(function ($sub) use ($now) {
                    $sub->whereColumn('closing_time', '<', 'opening_time')
                        ->where(function ($time) use ($now) {
                            $time->where('opening_time', '<=', $now)
                                ->orWhere('closing_time', '>=', $now);
                        });
                });
            });
        }

        // Sorting by nearest (validated lat/lng)
        if (($validated['sort'] ?? null) === 'nearest' && ! empty($validated['lat']) && ! empty($validated['lng'])) {
            $lat = (float) $validated['lat'];
            $lng = (float) $validated['lng'];
            $query->select('*')
                ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
                ->orderBy('distance');
        } else {
            $query->latest();
        }

        $cafes = $query->with('facilities')->get();

        // Cache facilities for 1 hour since they rarely change
        $allFacilities = \Illuminate\Support\Facades\Cache::remember('all_facilities', now()->addHour(), function () {
            return \App\Models\Facility::all();
        });

        return view('explore', compact('cafes', 'allFacilities'));
    }
}
