<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Tongkrongan;
use App\Models\TongkronganItem;
use App\Models\TongkronganVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class TongkronganController extends Controller
{
    /**
     * Search cafes for Tongkrongan list creation.
     */
    public function searchCafes(Request $request)
    {
        $data = $request->validate([
            'q' => 'nullable|string|max:100',
            'selected' => 'sometimes|array|max:5',
            'selected.*' => 'integer|min:1',
        ]);
        $search = $data['q'] ?? '';
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // selected param can be passed to exclude already selected cafes
        $selected = $data['selected'] ?? [];

        $cafes = Cafe::where('status', 'published')
            ->where('name', 'like', "%{$search}%")
            ->when(! empty($selected), function ($query) use ($selected) {
                $query->whereNotIn('id', $selected);
            })
            ->select(['id', 'name', 'address', 'slug', 'image_path'])
            ->limit(5)
            ->get();

        return response()->json($cafes);
    }

    /**
     * Show the creation form.
     */
    public function create()
    {
        return view('vue', ['title' => 'Buat daftar tongkrongan']);
    }

    /**
     * Store a new tongkrongan list.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'cafe_ids' => 'required|array|min:2|max:5',
            'cafe_ids.*' => ['integer', 'distinct', Rule::exists('cafes', 'id')->where('status', 'published')->whereNull('deleted_at')],
        ]);

        // Secure fingerprint pake session ID bawaan Laravel biar ga bisa di-spoof
        $fingerprint = $request->session()->getId();

        // Rate limit: 3 lists per IP per day
        $rateLimitKey = 'tongkrongan:'.$request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return response()->json([
                'message' => 'Maksimal 3 list per hari. Coba lagi besok!',
            ], 429);
        }

        $tongkrongan = DB::transaction(function () use ($validated, $fingerprint) {
            $list = Tongkrongan::create([
                'title' => $validated['title'],
                'creator_fingerprint' => $fingerprint,
                'creator_user_id' => auth()->id(),
            ]);
            $list->items()->createMany(array_map(fn ($id) => ['cafe_id' => $id], $validated['cafe_ids']));

            return $list;
        });

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
    public function show(Request $request, Tongkrongan $tongkrongan)
    {
        if (! $request->expectsJson()) {
            return view('vue', ['title' => $tongkrongan->title]);
        }

        $items = $tongkrongan->items()->whereHas('cafe', fn ($query) => $query->where('status', 'published'))
            ->with('cafe:id,name,slug,address')->withCount('votes')
            ->withExists(['votes as voted' => fn ($query) => $query->where('voter_fingerprint', $request->session()->getId())])->get();

        return [
            'title' => $tongkrongan->title, 'expired' => $tongkrongan->is_expired,
            'items' => $items->map(fn ($item) => [
                'id' => $item->id, 'name' => $item->cafe->name, 'address' => $item->cafe->address,
                'url' => route('cafes.show', $item->cafe, false), 'votes' => $item->votes_count, 'voted' => $item->voted,
            ]),
        ];
    }

    /**
     * Get updated votes for a tongkrongan.
     */
    public function getVotes(Tongkrongan $tongkrongan)
    {
        $votes = $tongkrongan->items()->whereHas('cafe', fn ($query) => $query->where('status', 'published'))
            ->withCount('votes')->get()->pluck('votes_count', 'id');

        return response()->json(['votes' => $votes]);
    }

    /**
     * Cast a vote on a tongkrongan item.
     */
    public function vote(Request $request, Tongkrongan $tongkrongan, TongkronganItem $item)
    {
        abort_unless($item->tongkrongan_id === $tongkrongan->id && $item->cafe?->status === 'published', 404);
        if ($tongkrongan->is_expired) {
            return response()->json(['message' => 'List ini sudah expired.'], 422);
        }

        // Secure fingerprint pake session ID bawaan Laravel biar ga bisa di-spoof
        $fingerprint = $request->session()->getId();

        // Check if already voted for this item
        $existing = TongkronganVote::where('tongkrongan_item_id', $item->id)
            ->where('voter_fingerprint', $fingerprint)
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
            'voter_fingerprint' => $fingerprint,
            'voter_user_id' => auth()->id(),
        ]);

        return response()->json([
            'action' => 'added',
            'vote_count' => $item->votes()->count(),
        ]);
    }
}
