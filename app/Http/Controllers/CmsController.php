<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Cafe;
use App\Models\City;
use App\Models\Facility;
use App\Models\Information;
use App\Models\Roastery;
use App\Models\User;
use App\Traits\OptimizesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

class CmsController extends Controller
{
    use OptimizesImages;

    private const RESOURCES = [
        'cafes' => [Cafe::class, 'Kedai kopi', 'name'],
        'roasteries' => [Roastery::class, 'Roastery', 'name'],
        'information' => [Information::class, 'Kabar kopi', 'title'],
        'cities' => [City::class, 'Kota', 'name'],
        'facilities' => [Facility::class, 'Fasilitas', 'name'],
        'users' => [User::class, 'Akun', 'name'],
    ];

    public function home(Request $request)
    {
        abort_unless($request->user()->canAccessCms(), 403);
        if (! $request->expectsJson()) {
            return view('vue', ['title' => 'Kelola WadahNgopi']);
        }

        return [
            'resources' => collect(self::RESOURCES)->filter(fn ($entry) => $this->allowed('viewAny', $entry[0]))
                ->map(fn ($entry, $key) => ['key' => $key, 'label' => $entry[1]])->values(),
            'account' => $request->user()->name,
            'role' => UserRole::tryFrom((string) $request->user()->role)?->label() ?? $request->user()->role,
            'can_publish' => $request->user()->hasRole(UserRole::Developer),
        ];
    }

    public function index(Request $request, string $resource)
    {
        [$class, , $name] = $this->resource($resource);
        $filters = $request->validate(['search' => 'nullable|string|max:100', 'page' => 'nullable|integer|between:1,1000']);
        $search = $filters['search'] ?? '';
        $query = $class::query();
        if (! $request->user()->hasRole(UserRole::Developer)) {
            if (in_array($class, [Cafe::class, Roastery::class], true)) {
                $query->where('owner_id', $request->user()->id);
            } elseif ($class === Facility::class) {
                $query->whereHas('cafe', fn ($query) => $query->where('owner_id', $request->user()->id));
            }
        }

        return [
            'items' => $query->when($search, fn ($query) => $query->where($name, 'like', '%'.addcslashes($search, '%_\\').'%'))
                ->latest('id')->simplePaginate(20)->through(fn (Model $record) => [
                    'id' => $record->id,
                    'name' => $record->{$name},
                    'detail' => $record->email ?? $record->address ?? $record->category ?? null,
                    'status' => $record->status ?? ($record instanceof Information ? ($record->is_published ? 'published' : 'draft') : null),
                ]),
            'fields' => $this->fields($class),
            'create' => $this->allowed('create', $class),
        ];
    }

    public function show(string $resource, int $id)
    {
        $record = $this->record($resource, $id, 'view');

        return [
            'record' => $record->only(array_diff(array_column($this->fields($record::class), 'name'), ['password'])) + ['id' => $record->id],
            'update' => $this->allowed('update', $record),
            'delete' => $this->allowed('delete', $record) && ! ($record instanceof User && $record->is(auth()->user())),
        ];
    }

    public function store(Request $request, string $resource)
    {
        [$class] = $this->resource($resource);
        $this->authorizeResource('create', $class);

        return response()->json(['id' => $this->save($request, new $class)->id], 201);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $record = $this->record($resource, $id, 'update');

        return ['id' => $this->save($request, $record)->id];
    }

    public function destroy(Request $request, string $resource, int $id)
    {
        $record = $this->record($resource, $id, 'delete');
        if (($record instanceof User && ($record->is($request->user()) || $record->cafes()->withTrashed()->exists() || $record->roasteries()->withTrashed()->exists()))
            || ($record instanceof City && ($record->cafes()->withTrashed()->exists() || Roastery::withTrashed()->where('city_id', $record->id)->exists()))) {
            throw ValidationException::withMessages(['record' => 'Pindahkan data yang masih terhubung sebelum menghapusnya.']);
        }
        $record->delete();

        return response()->noContent();
    }

    private function resource(string $key): array
    {
        abort_unless(isset(self::RESOURCES[$key]), 404);
        $entry = self::RESOURCES[$key];
        $this->authorizeResource('viewAny', $entry[0]);

        return $entry;
    }

    private function record(string $resource, int $id, string $action): Model
    {
        [$class] = $this->resource($resource);
        $record = $class::findOrFail($id);
        $this->authorizeResource($action, $record);

        return $record;
    }

    private function allowed(string $action, Model|string $target): bool
    {
        if ($target === Information::class || $target instanceof Information) {
            return auth()->user()->hasRole(UserRole::Developer);
        }

        return Gate::allows($action, $target);
    }

    private function authorizeResource(string $action, Model|string $target): void
    {
        abort_unless($this->allowed($action, $target), 403);
    }

    private function fields(string $class): array
    {
        $field = fn ($name, $label, $type = 'text', $options = []) => compact('name', 'label', 'type', 'options');

        if (in_array($class, [Cafe::class, Roastery::class], true)) {
            $fields = [
                $field('name', 'Nama tempat'), $field('address', 'Alamat', 'textarea'),
                $field('city_id', 'Kota', 'select', City::orderBy('name')->pluck('name', 'id')),
                $field('description', 'Deskripsi', 'textarea'),
                $field('status', 'Status publikasi', 'select', ['draft' => 'Draf', 'review' => 'Perlu ditinjau'] +
                    (auth()->user()->hasRole(UserRole::Developer) ? ['published' => 'Tayang'] : [])),
                $field('google_maps_url', 'Tautan Google Maps', 'url'),
                $field('whatsapp_number', 'Nomor WhatsApp'),
                $field('latitude', 'Lintang', 'number'), $field('longitude', 'Bujur', 'number'),
                $field('is_24_hours', 'Buka 24 jam', 'checkbox'),
                $field('operating_hours', 'Jam operasional', 'hours'),
                $field('social_links', 'Tautan media sosial', 'links'),
                $field('image_path', 'Foto utama', 'file'),
                $field('images', 'Galeri', 'gallery'), $field('menu_images', 'Foto menu', 'gallery'),
            ];
            if (auth()->user()->hasRole(UserRole::Developer)) {
                $fields[] = $field('owner_id', 'Pemilik (ID akun)', 'number');
            }
            if ($class === Cafe::class) {
                $fields[] = $field('has_wifi', 'Tersedia WiFi', 'checkbox');
            }

            return $fields;
        }

        return match ($class) {
            Information::class => [
                $field('title', 'Judul'), $field('slug', 'Slug'), $field('category', 'Kategori'),
                $field('summary', 'Ringkasan', 'textarea'), $field('content', 'Isi artikel (HTML)', 'textarea'),
                $field('source_name', 'Nama sumber'), $field('source_url', 'Tautan sumber', 'url'),
                $field('image_path', 'Foto sampul', 'file'), $field('is_published', 'Tayangkan', 'checkbox'),
                $field('published_at', 'Tanggal tayang', 'datetime-local'),
            ],
            City::class => [$field('name', 'Nama kota'), $field('slug', 'Slug')],
            Facility::class => [
                $field('cafe_id', 'Kedai kopi', 'select', Cafe::query()
                    ->when(! auth()->user()->hasRole(UserRole::Developer), fn ($query) => $query->where('owner_id', auth()->id()))
                    ->orderBy('name')->pluck('name', 'id')),
                $field('name', 'Nama fasilitas'), $field('icon', 'Nama ikon'),
            ],
            User::class => [
                $field('name', 'Nama'), $field('email', 'Email', 'email'),
                $field('role', 'Peran', 'select', collect(UserRole::cases())->mapWithKeys(fn ($role) => [$role->value => $role->label()])),
                $field('password', 'Kata sandi baru', 'password'),
            ],
        };
    }

    private function save(Request $request, Model $record): Model
    {
        $user = $request->user();
        $developer = $user->hasRole(UserRole::Developer);
        $place = $record instanceof Cafe || $record instanceof Roastery;
        $imageRule = ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp', 'gif'])->max('5mb'), 'dimensions:max_width=6000,max_height=6000'];
        $rules = match (true) {
            $place => [
                'name' => 'required|string|max:255', 'address' => 'nullable|string|max:1000',
                'city_id' => 'required|integer|exists:cities,id', 'description' => 'nullable|string|max:25000',
                'status' => ['sometimes', Rule::in($developer ? ['draft', 'review', 'published'] : ['draft', 'review'])],
                'google_maps_url' => 'nullable|url:http,https|max:2000', 'whatsapp_number' => 'nullable|string|max:30',
                'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
                'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
                'is_24_hours' => 'sometimes|boolean', 'has_wifi' => $record instanceof Cafe ? 'sometimes|boolean' : 'prohibited',
                'operating_hours' => 'sometimes|array:weekday,weekend',
                'operating_hours.*' => 'array:open,close',
                'operating_hours.*.*' => 'nullable|date_format:H:i',
                'social_links' => 'sometimes|array|max:10',
                'social_links.*' => 'array:platform,url,show',
                'social_links.*.platform' => 'required|string|max:30',
                'social_links.*.url' => 'required|url:http,https|max:2000',
                'social_links.*.show' => 'sometimes|boolean',
                'replace_links' => 'sometimes|boolean',
                'images_keep' => 'sometimes|json|max:300',
                'menu_images_keep' => 'sometimes|json|max:300',
                'image_upload' => $imageRule, 'images_upload' => 'sometimes|array|max:10',
                'images_upload.*' => $imageRule, 'menu_images_upload' => 'sometimes|array|max:10', 'menu_images_upload.*' => $imageRule,
                'owner_id' => $developer ? 'sometimes|integer|exists:users,id' : 'prohibited',
            ],
            $record instanceof Information => [
                'title' => 'required|string|max:255', 'slug' => $this->slugRule($record),
                'category' => 'required|string|max:60', 'summary' => 'nullable|string|max:2000',
                'content' => 'required|string|max:65000', 'source_name' => 'nullable|string|max:255',
                'source_url' => 'nullable|url:http,https|max:2000', 'is_published' => 'sometimes|boolean', 'published_at' => 'nullable|date',
                'image_upload' => $imageRule,
            ],
            $record instanceof City => ['name' => 'required|string|max:100', 'slug' => $this->slugRule($record)],
            $record instanceof Facility => [
                'cafe_id' => ['required', Rule::exists('cafes', 'id')->whereNull('deleted_at')
                    ->when(! $developer, fn ($rule) => $rule->where('owner_id', $user->id))],
                'name' => 'required|string|max:100', 'icon' => 'nullable|alpha_dash|max:80',
            ],
            $record instanceof User => [
                'name' => 'required|string|max:100',
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($record->id)],
                'role' => ['required', Rule::in(UserRole::values())],
                'password' => [$record->exists ? 'nullable' : 'required', 'string', 'min:8', 'max:72'],
            ],
        };
        $data = $request->validate($rules);
        if ($record instanceof User) {
            if ($record->is($user) && $data['role'] !== UserRole::Developer->value) {
                throw ValidationException::withMessages(['role' => 'Akun aktif tidak bisa mengubah perannya sendiri.']);
            }
            $data['email'] = Str::lower($data['email']);
            if (empty($data['password'])) {
                unset($data['password']);
            }
        }
        if ($place) {
            $data['description'] = isset($data['description']) ? clean($data['description']) : ($data['description'] ?? null);
            if ($data['replace_links'] ?? false) {
                $data['social_links'] ??= [];
            }
            unset($data['replace_links']);
            foreach (['images', 'menu_images'] as $field) {
                if (array_key_exists($field.'_keep', $data)) {
                    $indices = json_decode($data[$field.'_keep'], true);
                    if (! is_array($indices) || ! array_is_list($indices) || array_filter($indices, fn ($index) => ! is_int($index))) {
                        throw ValidationException::withMessages([$field => 'Daftar foto tidak valid.']);
                    }
                    $data[$field] = array_values(array_intersect_key(array_values($record->{$field} ?? []), array_flip($indices)));
                    unset($data[$field.'_keep']);
                }
            }
            if (! $record->exists) {
                $data['owner_id'] ??= $user->id;
            } elseif (! $developer && $record->status === 'published') {
                $data['status'] ??= 'review';
            }
        }

        $uploaded = [];
        try {
            foreach (['image_upload' => 'image_path', 'images_upload' => 'images', 'menu_images_upload' => 'menu_images'] as $input => $field) {
                if (! isset($data[$input])) {
                    continue;
                }
                $paths = [];
                foreach (is_array($data[$input]) ? $data[$input] : [$data[$input]] as $image) {
                    if ($image) {
                        $paths[] = $uploaded[] = $this->optimizeAndStoreImage($image, $record->getTable());
                    }
                }
                if ($paths) {
                    $data[$field] = $field === 'image_path' ? $paths[0] : array_merge($data[$field] ?? $record->{$field} ?? [], $paths);
                }
                unset($data[$input]);
            }

            return DB::transaction(function () use ($record, $data) {
                $record->fill($data)->save();

                return $record;
            });
        } catch (\Throwable $error) {
            Storage::disk('public')->delete($uploaded);
            throw $error;
        }
    }

    private function slugRule(Model $record): array
    {
        return ['required', 'alpha_dash:ascii', 'max:255', Rule::unique($record->getTable())->ignore($record->id)];
    }
}
