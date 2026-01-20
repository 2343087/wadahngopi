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
        $cafes = Cafe::latest()->get();

        return view('home', compact('cafes'));
    }

    /**
     * Display the specified cafe.
     */
    public function show(Cafe $cafe): View
    {
        $cafe->load(['menus', 'reviews']);

        return view('cafes.show', compact('cafe'));
    }
}
