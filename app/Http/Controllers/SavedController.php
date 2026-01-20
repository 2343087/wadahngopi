<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedController extends Controller
{
    public function index(Request $request): View
    {
        $ids = $request->query('ids', []);

        $cafes = [];
        if (! empty($ids)) {
            $cafes = Cafe::whereIn('id', $ids)->get();
        }

        return view('saved', compact('cafes'));
    }
}
