<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cafe::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->boolean('wifi')) {
            $query->where('has_wifi', true);
        }

        $cafes = $query->latest()->get();

        return view('explore', compact('cafes'));
    }
}
