<?php

use App\Models\Cafe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays the list of cafes on the home page', function () {
    $cafe = Cafe::factory()->create(['name' => 'Wadah Kopi Pusat']);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Wadah Kopi Pusat');
});

it('displays the cafe detail page', function () {
    $cafe = Cafe::factory()->create(['name' => 'Kopi Sore']);

    $response = $this->get("/cafes/{$cafe->id}");

    $response->assertStatus(200);
    $response->assertSee('Kopi Sore');
});
