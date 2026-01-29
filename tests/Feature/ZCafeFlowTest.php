<?php

use App\Models\Cafe;
beforeEach(fn() => Cache::flush());

it('displays the landing page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('WadahNgopi');
});

it('displays the list of cafes on the explore page', function () {
    $cafe = Cafe::factory()->create(['name' => 'Wadah Kopi Pusat', 'status' => 'published']);

    $response = $this->get('/explore');

    $response->assertStatus(200);
    $response->assertSee('Wadah Kopi Pusat');
});

it('displays the cafe detail page', function () {
    $cafe = Cafe::factory()->create(['name' => 'Kopi Sore', 'status' => 'published']);

    $this->withoutExceptionHandling();
    $response = $this->get("/cafes/{$cafe->id}");

    $response->assertStatus(200);
    $response->assertSee('Kopi Sore');
});
