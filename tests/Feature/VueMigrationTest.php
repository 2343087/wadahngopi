<?php

use App\Models\Cafe;
use App\Models\City;
use App\Models\Information;
use App\Models\Roastery;
use App\Models\Tongkrongan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uses one Vue entry for the public directory and login', function (string $path) {
    $this->get($path)->assertOk()->assertSee('id="app"', false)->assertHeader('Vary', 'Accept');
})->with(['/', '/explore', '/roastery', '/saved', '/information', '/admin/login', '/admin/register', '/tongkrongan/buat']);

it('lists published places and supports search, city, alphabetical order and pagination', function ($model, $path) {
    $city = City::factory()->create();
    $other = City::factory()->create();
    $model::factory()->create(['city_id' => $city->id, 'status' => 'published', 'name' => 'Aroma Samarinda']);
    $alpha = $model::factory()->create(['city_id' => $city->id, 'status' => 'published', 'name' => 'Alfa Samarinda']);
    $model::factory()->create(['city_id' => $other->id, 'status' => 'published', 'name' => 'Awan Samarinda']);
    $model::factory()->create(['city_id' => $city->id, 'status' => 'draft', 'name' => 'A Draf Samarinda']);
    $this->getJson($path.'?'.http_build_query(['search' => 'Samarinda', 'city' => $city->id, 'letter' => 'A', 'sort' => 'name_az']))
        ->assertOk()->assertJsonCount(2, 'items.data')->assertJsonPath('items.data.0.id', $alpha->id);
    $this->getJson($path.'?sort=unknown')->assertUnprocessable();
    $this->getJson($path.'?search='.str_repeat('z', 101))->assertUnprocessable();
    $this->getJson($path.'?page=1001')->assertUnprocessable();
})->with([[Cafe::class, '/explore'], [Roastery::class, '/roastery']]);

it('validates and orders nearby places with listing fields intact', function () {
    $cafe = Cafe::factory()->create(['status' => 'published', 'latitude' => -0.501, 'longitude' => 117.15]);
    $this->getJson('/explore?sort=nearest&lat=-0.5&lng=117.15')->assertOk()->assertJsonPath('items.data.0.id', $cafe->id);
    $this->getJson('/explore?sort=nearest&lat=93&lng=117.15')->assertUnprocessable();
});

it('loads detail data only for a published place', function ($model, $name) {
    $item = $model::factory()->create(['status' => 'published', 'is_24_hours' => true]);
    $draft = $model::factory()->create(['status' => 'draft']);
    $this->getJson(route($name, $item))->assertOk()->assertJsonPath('open', true)->assertJsonPath('name', $item->name);
    $this->getJson(route($name, $draft))->assertNotFound();
})->with([[Cafe::class, 'cafes.show'], [Roastery::class, 'roastery.show']]);

it('selects an open published cafe for roulette', function () {
    Cafe::factory()->create(['status' => 'draft', 'is_24_hours' => true]);
    $this->getJson('/api/cafe-roulette')->assertOk()->assertJsonPath('item', null);
    $cafe = Cafe::factory()->create(['status' => 'published', 'is_24_hours' => true]);
    $this->getJson('/api/cafe-roulette')->assertOk()->assertJsonPath('item.id', $cafe->id);
});

it('filters published news by category and exposes popular order', function () {
    $article = Information::factory()->create(['category' => 'Berita', 'is_published' => true, 'views' => 250]);
    Information::factory()->create(['category' => 'Berita', 'is_published' => true, 'views' => 10]);
    Information::factory()->create(['category' => 'Berita', 'is_published' => false]);
    Information::factory()->create(['category' => 'Promo', 'is_published' => true]);
    $this->getJson('/information?category=Berita&sort=popular')->assertOk()->assertJsonCount(2, 'items.data')->assertJsonPath('items.data.0.id', $article->id);
});

it('returns only selected published saved places and caps client IDs', function () {
    $cafe = Cafe::factory()->create(['status' => 'published']);
    $draft = Cafe::factory()->create(['status' => 'draft']);
    $roastery = Roastery::factory()->create(['status' => 'published']);
    $this->getJson('/saved')->assertOk()->assertJsonPath('items', []);
    $this->getJson('/saved?'.http_build_query(['cafes' => [$cafe->id, $draft->id], 'roasteries' => [$roastery->id]]))
        ->assertOk()->assertJsonCount(2, 'items')->assertJsonPath('items.0.kind', 'cafe')->assertJsonPath('items.1.kind', 'roastery');
    $this->getJson('/saved?'.http_build_query(['cafes' => range(1, 51)]))->assertUnprocessable();
});

it('prevents votes using an item outside the requested list', function () {
    $a = Tongkrongan::create(['title' => 'Daftar A']);
    $b = Tongkrongan::create(['title' => 'Daftar B']);
    $cafe = Cafe::factory()->create(['status' => 'published']);
    $item = $b->items()->create(['cafe_id' => $cafe->id]);
    $this->postJson(route('tongkrongan.vote', [$a, $item]))->assertNotFound();
    $this->postJson(route('tongkrongan.vote', [$b, $item]))->assertOk()->assertJsonPath('action', 'added');
    $this->getJson(route('tongkrongan.show', $b))->assertOk()->assertJsonPath('items.0.votes', 1);
});

it('does not give access to draft cafe interactions', function () {
    $cafe = Cafe::factory()->create(['status' => 'draft']);
    $this->actingAs(User::factory()->create());
    $this->getJson(route('cafes.vibe.show', $cafe->id))->assertNotFound();
    $this->postJson(route('cafes.check-in', $cafe->id))->assertNotFound();
    $this->postJson('/api/bookmarks/toggle', ['bookmarkable_id' => $cafe->id, 'bookmarkable_type' => 'cafe', 'saved' => true])->assertNotFound();
});

it('keeps owner permissions and publication review in the CMS', function () {
    $owner = User::factory()->create(['role' => 'admin']);
    $mine = Cafe::factory()->create(['owner_id' => $owner->id, 'status' => 'published']);
    $other = Cafe::factory()->create(['status' => 'published']);
    $this->actingAs($owner)->getJson('/admin/data/cafes')->assertOk()->assertJsonCount(1, 'items.data');
    $this->getJson('/admin/data/cafes/'.$other->id)->assertForbidden();
    $this->getJson('/admin/data/users')->assertForbidden();
    $data = ['name' => 'Nama baru', 'city_id' => $mine->city_id];
    $this->patchJson('/admin/data/cafes/'.$mine->id, $data + ['status' => 'published'])->assertUnprocessable();
    $this->patchJson('/admin/data/cafes/'.$mine->id, $data)->assertOk();
    expect($mine->refresh()->status)->toBe('review');
});

it('does not expose account hashes in the CMS', function () {
    $developer = User::factory()->create(['role' => 'developer']);
    $this->actingAs($developer)->getJson('/admin/data/users/'.$developer->id)
        ->assertOk()->assertJsonMissingPath('record.password');
});

it('registers a visitor and rejects developer registration', function () {
    $data = ['name' => 'Pendaftar', 'email' => 'pendaftar@example.test', 'password' => 'KopiSore2026', 'password_confirmation' => 'KopiSore2026'];
    $this->postJson('/admin/register', $data + ['role' => 'developer'])->assertUnprocessable();
    $this->postJson('/admin/register', $data + ['role' => 'user'])->assertCreated()->assertJsonPath('redirect', '/saved');
    $this->assertAuthenticated();
    $this->getJson('/admin')->assertForbidden();
});

it('creates, publishes and removes an article in the CMS', function () {
    Storage::fake('public');
    $developer = User::factory()->create(['role' => 'developer']);
    $this->actingAs($developer)->getJson('/admin')->assertOk()->assertJsonCount(6, 'resources');

    $article = [
        'title' => 'Berita kopi', 'slug' => 'berita-kopi', 'category' => 'Berita',
        'content' => '<p>Kopi Samarinda</p>', 'is_published' => false,
    ];
    $result = $this->post('/admin/data/information', $article + ['image_upload' => UploadedFile::fake()->image('cover.jpg')], ['Accept' => 'application/json'])
        ->assertCreated();
    $id = $result->json('id');
    Storage::disk('public')->assertExists(Information::findOrFail($id)->image_path);
    $this->getJson('/information/berita-kopi')->assertNotFound();
    $this->patchJson('/admin/data/information/'.$id, array_replace($article, ['is_published' => true]))->assertOk();
    $this->getJson('/information/berita-kopi')->assertOk()->assertJsonPath('name', 'Berita kopi');
    $this->deleteJson('/admin/data/information/'.$id)->assertNoContent();
    $this->getJson('/information/berita-kopi')->assertNotFound();
});

it('keeps bookmarks stable on repeated save and removal requests', function () {
    $user = User::factory()->create();
    $cafe = Cafe::factory()->create(['status' => 'published']);
    $bookmark = ['bookmarkable_id' => $cafe->id, 'bookmarkable_type' => 'cafe'];
    $this->actingAs($user)->postJson('/api/bookmarks/toggle', $bookmark + ['saved' => true])->assertOk();
    $this->postJson('/api/bookmarks/toggle', $bookmark + ['saved' => true])->assertOk();
    $this->getJson('/saved')->assertOk()->assertJsonCount(1, 'items');
    $this->postJson('/api/bookmarks/toggle', $bookmark + ['saved' => false])->assertOk();
    $this->postJson('/api/bookmarks/toggle', $bookmark + ['saved' => false])->assertOk();
    $this->getJson('/saved')->assertOk()->assertJsonPath('items', []);
});
