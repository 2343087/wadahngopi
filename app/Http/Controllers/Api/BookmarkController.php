<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Cafe;
use App\Models\Roastery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookmarkController extends Controller
{
    /**
     * Get all bookmarks for authenticated user.
     */
    public function index(Request $request)
    {
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->bookmarkable_id,
                'type' => $b->bookmarkable_type,
            ]);

        return response()->json([
            'cafes' => $bookmarks->where('type', 'cafe')->pluck('id')->values(),
            'roasteries' => $bookmarks->where('type', 'roastery')->pluck('id')->values(),
        ]);
    }

    /**
     * Toggle a bookmark (add or remove).
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'bookmarkable_id' => 'required|integer|min:1',
            'bookmarkable_type' => 'required|in:cafe,roastery',
            'saved' => 'sometimes|boolean',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $key = [
                'bookmarkable_id' => $validated['bookmarkable_id'],
                'bookmarkable_type' => $validated['bookmarkable_type'],
            ];
            $existing = $user->bookmarks()->where($key)->first();
            if (! ($validated['saved'] ?? ! $existing)) {
                $existing?->delete();

                return ['action' => 'removed', 'message' => 'Dihapus dari simpanan.'];
            }
            if (! $existing) {
                $model = $key['bookmarkable_type'] === 'cafe' ? Cafe::class : Roastery::class;
                abort_unless($model::whereKey($key['bookmarkable_id'])->where('status', 'published')->exists(), 404);
                if ($user->bookmarks()->count() >= 50) {
                    throw ValidationException::withMessages(['saved' => 'Maksimal 50 simpanan.']);
                }
                $user->bookmarks()->create($key);
            }

            return ['action' => 'added', 'message' => 'Ditambahkan ke simpanan.'];
        });
    }

    /**
     * Sync bookmarks from localStorage (one-time migration).
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'cafes' => 'nullable|array|max:50',
            'cafes.*' => 'integer|min:1',
            'roasteries' => 'nullable|array|max:50',
            'roasteries.*' => 'integer|min:1',
        ]);

        $synced = DB::transaction(function () use ($request, $validated) {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $available = max(0, 50 - $user->bookmarks()->count());
            $added = 0;
            foreach (['cafe' => Cafe::class, 'roastery' => Roastery::class] as $kind => $model) {
                $ids = $model::whereKey($validated[$kind === 'cafe' ? 'cafes' : 'roasteries'] ?? [])
                    ->where('status', 'published')->pluck('id');
                foreach ($ids as $id) {
                    if ($added >= $available) {
                        return $added;
                    }
                    $bookmark = $user->bookmarks()->firstOrCreate(['bookmarkable_id' => $id, 'bookmarkable_type' => $kind]);
                    $added += (int) $bookmark->wasRecentlyCreated;
                }
            }

            return $added;
        });

        return ['synced' => $synced];
    }
}
