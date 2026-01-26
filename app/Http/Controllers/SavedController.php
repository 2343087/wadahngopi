<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedController extends Controller
{
    public function index(Request $request): View
    {
        $ids = is_array($request->query('ids')) ? $request->query('ids') : [];

        // Security: Limit IDs to prevent DOS (max 50)
        $ids = array_slice($ids, 0, 50);

        $cafes = collect([]);
        if (! empty($ids)) {
            $cafes = Cafe::where('status', 'published')->whereIn('id', $ids)->get();
        }

        return view('saved', compact('cafes'));
    }
}
