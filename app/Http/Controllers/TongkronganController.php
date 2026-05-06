<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Tongkrongan;
use App\Models\TongkronganItem;
use App\Models\TongkronganVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TongkronganController extends Controller
{
    /**
     * Show the creation form.
     */
    public function create()
    {
        return view('tongkrongan.create');
    }

    /**
     * Store a new tongkrongan list.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'cafe_ids' => 'required|array|min:2|max:5',
            'cafe_ids.*' => 'integer|exists:cafes,id',
            'fingerprint' => 'required|string|max:64',
        ]);

        // Rate limit: 3 lists per IP per day
        $rateLimitKey = 'tongkrongan:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return response()->json([
                'message' => 'Maksimal 3 list per hari. Coba lagi besok!',
            ], 429);
        }

        $tongkrongan = Tongkrongan::create([
            'title' => $validated['title'],
            'creator_fingerprint' => $validated['fingerprint'],
            'creator_user_id' => auth()->id(),
        ]);

        foreach ($validated['cafe_ids'] as $cafeId) {
            TongkronganItem::create([
                'tongkrongan_id' => $tongkrongan->id,
                'cafe_id' => $cafeId,
            ]);
        }

        RateLimiter::hit($rateLimitKey, 86400);

        return response()->json([
            'message' => 'List berhasil dibuat!',
            'uuid' => $tongkrongan->uuid,
            'share_url' => $tongkrongan->share_url,
        ]);
    }

    /**
     * Show a shared tongkrongan.
     */
    public function show(Tongkrongan $tongkrongan)
    {
        if ($tongkrongan->is_expired) {
            return view('tongkrongan.expired', compact('tongkrongan'));
        }

        $tongkrongan->load([
            'items.cafe' => fn($q) => $q->select(['id', 'name', 'slug', 'address', 'image_path', 'is_24_hours', 'operating_hours', 'weekday_open', 'weekday_close', 'weekend_open', 'weekend_close']),
            'items.votes',
        ]);

        return view('tongkrongan.show', compact('tongkrongan'));
    }

    /**
     * Cast a vote on a tongkrongan item.
     */
    public function vote(Request $request, Tongkrongan $tongkrongan, TongkronganItem $item)
    {
        if ($tongkrongan->is_expired) {
            return response()->json(['message' => 'List ini sudah expired.'], 422);
        }

        $validated = $request->validate([
            'fingerprint' => 'required|string|max:64',
        ]);

        // Check if already voted for this item
        $existing = TongkronganVote::where('tongkrongan_item_id', $item->id)
            ->where('voter_fingerprint', $validated['fingerprint'])
            ->first();

        if ($existing) {
            // Toggle: remove vote
            $existing->delete();
            return response()->json([
                'action' => 'removed',
                'vote_count' => $item->votes()->count(),
            ]);
        }

        TongkronganVote::create([
            'tongkrongan_item_id' => $item->id,
            'voter_fingerprint' => $validated['fingerprint'],
            'voter_user_id' => auth()->id(),
        ]);

        return response()->json([
            'action' => 'added',
            'vote_count' => $item->votes()->count(),
        ]);
    }
}
