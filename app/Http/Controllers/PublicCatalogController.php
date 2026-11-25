<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\City;
use App\Models\Facility;
use App\Models\Information;
use App\Models\Roastery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PublicCatalogController extends Controller
{
    public function home(Request $request)
    {
        if (! $request->expectsJson()) {
            return view('vue', ['title' => 'WadahNgopi | Kopi Samarinda']);
        }

        return [
            'items' => Cafe::where('status', 'published')->whereNotNull('image_path')
                ->with('city')->orderByRaw("CASE WHEN image_path LIKE 'https://images.unsplash.com/%' THEN 1 ELSE 0 END")
                ->inRandomOrder()->limit(12)->get()->map(fn (Cafe $cafe) => $this->venue($cafe)),
            'news' => Information::where('is_published', true)->latest('published_at')->limit(3)->get()
                ->map(fn (Information $article) => $this->articleData($article)),
        ];
    }

    public function cafes(Request $request)
    {
        return $this->catalog($request, Cafe::class, 'Cari kedai kopi');
    }

    public function roasteries(Request $request)
    {
        return $this->catalog($request, Roastery::class, 'Cari roastery');
    }

    private function catalog(Request $request, string $model, string $title)
    {
        if (! $request->expectsJson()) {
            return view('vue', compact('title'));
        }

        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
            'city' => 'nullable|integer|min:1',
            'facility' => 'nullable|string|max:100',
            'open_now' => 'nullable|boolean',
            'sort' => 'nullable|in:latest,nearest,name_az,name_za',
            'letter' => 'nullable|regex:/^[A-Z]$/',
            'lat' => 'required_if:sort,nearest|nullable|numeric|between:-90,90',
            'lng' => 'required_if:sort,nearest|nullable|numeric|between:-180,180',
            'page' => 'nullable|integer|between:1,1000',
        ]);

        $query = $model::query()->where('status', 'published')->with('city')
            ->when($filters['search'] ?? null, fn ($query, $term) => $query->search($term))
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->where('city_id', $city))
            ->when($filters['letter'] ?? null, fn ($query, $letter) => $query->where('name', 'like', $letter.'%'))
            ->when($filters['open_now'] ?? false, fn ($query) => $query->openNow());
        if ($model === Cafe::class && ! empty($filters['facility'])) {
            $query->whereHas('facilities', fn ($query) => $query->where('name', $filters['facility']));
        }
        if (($filters['sort'] ?? '') === 'nearest') {
            $query->nearest((float) $filters['lat'], (float) $filters['lng']);
        } elseif (in_array($filters['sort'] ?? '', ['name_az', 'name_za'], true)) {
            $query->orderBy('name', $filters['sort'] === 'name_za' ? 'desc' : 'asc');
        }

        return [
            'items' => $query->latest('id')->simplePaginate(15)->through(fn ($venue) => $this->venue($venue)),
            'cities' => City::orderBy('name')->get(['id', 'name']),
            'facilities' => $model === Cafe::class
                ? Facility::whereHas('cafe', fn ($query) => $query->where('status', 'published'))
                    ->orderBy('name')->distinct()->pluck('name')
                : [],
        ];
    }

    public function cafe(Request $request, Cafe $cafe)
    {
        abort_unless($cafe->status === 'published', 404);
        if (! $request->expectsJson()) {
            return view('vue', ['title' => $cafe->name]);
        }

        $user = $request->user();

        return $this->venue($cafe->load(['city', 'facilities']), true) + [
            'rating' => (float) $cafe->wfc_avg_score,
            'rating_count' => (int) $cafe->wfc_review_count,
            'has_rated' => $user && $cafe->wfcScores()->where('user_id', $user->id)->exists(),
            'checked_in' => $user && $user->checkIns()->where('cafe_id', $cafe->id)->whereDate('created_at', today())->exists(),
            'reviews' => $cafe->wfcScores()->with('user:id,name')->whereNotNull('comment')->latest()->limit(20)->get()
                ->map(fn ($review) => ['name' => $review->user?->name, 'comment' => $review->comment]),
        ];
    }

    public function roastery(Request $request, Roastery $roastery)
    {
        abort_unless($roastery->status === 'published', 404);

        return $request->expectsJson()
            ? $this->venue($roastery->load('city'), true)
            : view('vue', ['title' => $roastery->name]);
    }

    public function information(Request $request)
    {
        if (! $request->expectsJson()) {
            return view('vue', ['title' => 'Kabar kopi']);
        }
        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:60',
            'sort' => 'nullable|in:latest,popular',
            'page' => 'nullable|integer|between:1,1000',
        ]);
        $published = Information::where('is_published', true);

        return [
            'categories' => (clone $published)->orderBy('category')->distinct()->pluck('category'),
            'items' => $published
                ->when($filters['search'] ?? null, fn ($query, $term) => $query->where('title', 'like', '%'.addcslashes($term, '%_\\').'%'))
                ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
                ->when(($filters['sort'] ?? '') === 'popular', fn ($query) => $query->orderByDesc('views'))
                ->latest('published_at')->latest('id')->simplePaginate(12)
                ->through(fn (Information $article) => $this->articleData($article)),
        ];
    }

    public function article(Request $request, Information $information)
    {
        abort_unless($information->is_published, 404);
        RateLimiter::attempt("info_view_{$information->id}_{$request->ip()}", 1,
            fn () => Cache::increment("info_views:{$information->id}"), 60);
        if (! $request->expectsJson()) {
            return view('vue', ['title' => $information->title]);
        }

        return $this->articleData($information) + [
            'content_html' => clean($information->content ?? ''),
            'source' => self::webUrl($information->source_url),
            'source_name' => $information->source_name,
        ];
    }

    public function saved(Request $request)
    {
        if (! $request->expectsJson()) {
            return view('vue', ['title' => 'Tempat tersimpan']);
        }
        $data = $request->validate([
            'cafes' => 'nullable|array|max:50', 'cafes.*' => 'integer|min:1',
            'roasteries' => 'nullable|array|max:50', 'roasteries.*' => 'integer|min:1',
        ]);
        if ($request->user()) {
            $bookmarks = $request->user()->bookmarks()->limit(50)->get();
            $data['cafes'] = array_unique(array_merge($data['cafes'] ?? [], $bookmarks->where('bookmarkable_type', 'cafe')->pluck('bookmarkable_id')->all()));
            $data['roasteries'] = array_unique(array_merge($data['roasteries'] ?? [], $bookmarks->where('bookmarkable_type', 'roastery')->pluck('bookmarkable_id')->all()));
        }

        return [
            'items' => Cafe::where('status', 'published')->whereKey($data['cafes'] ?? [])->with('city')->get()
                ->concat(Roastery::where('status', 'published')->whereKey($data['roasteries'] ?? [])->with('city')->get())
                ->map(fn ($venue) => $this->venue($venue))->values(),
        ];
    }

    public function roulette()
    {
        $cafe = Cafe::where('status', 'published')->openNow()->with('city')->inRandomOrder()->first();

        return ['item' => $cafe ? $this->venue($cafe) : null];
    }

    private function venue(Cafe|Roastery $venue, bool $detail = false): array
    {
        $cafe = $venue instanceof Cafe;
        $data = [
            'id' => $venue->id, 'name' => $venue->name, 'address' => $venue->address,
            'kind' => $cafe ? 'cafe' : 'roastery', 'city' => $venue->city?->name,
            'url' => route($cafe ? 'cafes.show' : 'roastery.show', $venue, false),
            'image' => self::media($venue->image_path),
            'image_illustration' => parse_url($venue->image_path ?? '', PHP_URL_HOST) === 'images.unsplash.com',
            'open' => $venue->is_open,
            'hours' => $venue->today_hours, 'distance' => $venue->distance,
        ];
        if (! $detail) {
            return $data;
        }

        return $data + [
            'description_html' => clean($venue->description ?? ''),
            'gallery' => collect($venue->images)->map(fn ($path) => self::media(is_array($path) ? ($path['image'] ?? null) : $path))->filter()->unique()->values(),
            'menu' => collect($venue->menu_images)->filter(fn ($item) => ! is_array($item) || ($item['is_active'] ?? true))
                ->map(fn ($item) => ['image' => self::media(is_array($item) ? ($item['image'] ?? null) : $item), 'tag' => is_array($item) ? ($item['tag'] ?? null) : null])
                ->filter(fn ($item) => $item['image'])->values(),
            'maps' => self::webUrl($venue->google_maps_url),
            'phone' => preg_replace('/[^0-9]/', '', $venue->whatsapp_number ?? ''),
            'links' => collect($venue->social_links)->filter(fn ($link) => is_array($link) && ($link['show'] ?? true) && self::webUrl($link['url'] ?? null))->values(),
            'facilities' => $cafe ? $venue->facilities->pluck('name') : [],
        ];
    }

    private function articleData(Information $article): array
    {
        return [
            'id' => $article->id, 'name' => $article->title,
            'url' => route('information.show', ['information' => $article->slug], false),
            'image' => self::media($article->image_path), 'category' => $article->category,
            'summary' => Str::limit(strip_tags($article->summary ?: $article->short_content ?? ''), 180),
            'date' => $article->published_at?->toDateString(),
        ];
    }

    private static function media(?string $path): ?string
    {
        if (! $path || str_contains($path, '..')) {
            return null;
        }
        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        return self::webUrl($path) ?? (preg_match('~^[\w /.-]+$~', $path) ? '/storage/'.ltrim($path, '/') : null);
    }

    private static function webUrl(?string $url): ?string
    {
        return $url && filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : null;
    }
}
