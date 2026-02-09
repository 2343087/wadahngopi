<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roastery;
use Illuminate\View\View;

class RoasteryController extends Controller
{
    public function index(): View
    {
        return view('roastery.index');
    }

    public function show(Roastery $roastery): View
    {
        abort_if($roastery->status !== 'published', 404);

        // Cache individual roastery with relationships for 10 minutes
        $roastery = \Illuminate\Support\Facades\Cache::remember("roastery_{$roastery->id}", now()->addMinutes(10), function () use ($roastery) {
            $roastery->load('city');
            return $roastery;
        });

        return view('roastery.show', compact('roastery'));
    }
}
