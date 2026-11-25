<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\WfcScore;
use App\Services\WfcScoreService;
use Illuminate\Http\Request;

class WfcScoreController extends Controller
{
    protected $wfcService;

    public function __construct(WfcScoreService $wfcService)
    {
        $this->wfcService = $wfcService;
    }

    public function store(Request $request, Cafe $cafe)
    {
        abort_unless($cafe->status === 'published', 404);
        $validated = $request->validate([
            'wifi_rating' => 'required|integer|min:1|max:5',
            'outlet_rating' => 'required|integer|min:1|max:5',
            'comfort_rating' => 'required|integer|min:1|max:5',
            'user_lat' => 'nullable|required_with:user_lng|numeric|between:-90,90',
            'user_lng' => 'nullable|required_with:user_lat|numeric|between:-180,180',
            'comment' => 'nullable|string|max:500',
        ]);

        // Prevent multiple ratings from the same user for the same cafe
        $existing = WfcScore::where('cafe_id', $cafe->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Lu udah kasih rating buat cafe ini sebelumnya.',
            ], 422);
        }

        $score = $this->wfcService->submitScore($cafe, $validated);

        return response()->json([
            'message' => 'Score submitted successfully!',
            'is_verified' => $score->is_verified,
            'verification_reason' => $score->verification_reason ?? 'no_gps',
            'new_aggregate' => [
                'score' => number_format($cafe->fresh()->wfc_avg_score, 1),
                'count' => $cafe->fresh()->wfc_review_count,
            ],
        ]);
    }
}
