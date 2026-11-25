<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Services\VibeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class VibeController extends Controller
{
    public function __construct(protected VibeService $vibeService) {}

    /**
     * Get current vibe aggregate for a cafe.
     */
    public function show(Cafe $cafe)
    {
        abort_unless($cafe->status === 'published', 404);

        return response()->json($this->vibeService->getAggregatedVibe($cafe));
    }

    /**
     * Submit a vibe vote.
     */
    public function store(Request $request, Cafe $cafe)
    {
        abort_unless($cafe->status === 'published', 404);
        $validated = $request->validate([
            'level' => 'required|in:sepi,lumayan,rame,penuh',
            'user_lat' => 'nullable|required_with:user_lng|numeric|between:-90,90',
            'user_lng' => 'nullable|required_with:user_lat|numeric|between:-180,180',
        ]);

        // Secure fingerprint pake session ID bawaan Laravel biar ga bisa di-spoof
        $fingerprint = $request->session()->getId();

        // Rate limit: 1 vote per cafe per fingerprint per 4 hours
        $rateLimitKey = "vibe:{$cafe->id}:{$fingerprint}";
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return response()->json([
                'message' => 'Lu udah vote buat cafe ini. Tunggu beberapa jam lagi ya!',
            ], 429);
        }

        // Check for recent duplicate
        if ($this->vibeService->hasRecentVote($cafe->id, $fingerprint)) {
            return response()->json([
                'message' => 'Lu udah vote buat cafe ini dalam 4 jam terakhir.',
            ], 422);
        }

        $vote = $this->vibeService->submitVibe(
            $cafe,
            $validated['level'],
            $validated['user_lat'] ?? null,
            $validated['user_lng'] ?? null,
            $fingerprint,
            auth()->id()
        );

        // Set rate limit for 4 hours
        RateLimiter::hit($rateLimitKey, 14400);

        return response()->json([
            'message' => 'Vibe berhasil dicatat!',
            'is_verified' => $vote->is_verified,
            'aggregate' => $this->vibeService->getAggregatedVibe($cafe),
        ]);
    }
}
