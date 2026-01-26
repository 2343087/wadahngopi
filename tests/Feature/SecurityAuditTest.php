<?php

use App\Models\Cafe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cannot view draft cafe directly', function () {
    $cafe = Cafe::factory()->create(['status' => 'draft']);

    $response = $this->get(route('cafes.show', $cafe));

    $response->assertStatus(404);
});

test('can view published cafe', function () {
    $cafe = Cafe::factory()->create(['status' => 'published']);

    $response = $this->get(route('cafes.show', $cafe));

    $response->assertStatus(200);
});

test('saved cafes list excludes draft cafes', function () {
    $publishedCafe = Cafe::factory()->create(['status' => 'published']);
    $draftCafe = Cafe::factory()->create(['status' => 'draft']);

    $response = $this->get(route('saved', [
        'ids' => [$publishedCafe->id, $draftCafe->id],
    ]));

    $response->assertStatus(200);
    $response->assertSee($publishedCafe->name);
    $response->assertDontSee($draftCafe->name);
});

test('saved cafes list limits to 50 ids', function () {
    $cafes = Cafe::factory()->count(60)->create(['status' => 'published']);
    $ids = $cafes->pluck('id')->toArray();

    $response = $this->get(route('saved', [
        'ids' => $ids,
    ]));

    $response->assertStatus(200);
    // Since we only query 50, if we see the 51st cafe it means limit isn't working
    // But testing the exact count is better if we check characters/view data
    $viewCafes = $response->viewData('cafes');
    expect($viewCafes)->toHaveCount(50);
});

test('home page escapes cafe name for JS injection', function () {
    $maliciousName = 'Cafe <script>alert("xss")</script>';
    Cafe::factory()->create([
        'name' => $maliciousName,
        'status' => 'published',
    ]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    // Js::from() will escape the string properly (e.g. < becomes \u003C)
    $response->assertDontSee($maliciousName, false);
    $response->assertSee('u003Cscript', false); // Verify it's escaped
});
