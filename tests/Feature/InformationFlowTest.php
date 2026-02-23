<?php

use App\Models\Information;

it('increments view count on first visit', function () {
    $info = Information::factory()->create(['is_published' => true, 'views' => 0]);

    $this->get(route('information.show', $info));

    expect($info->fresh()->views)->toBe(1);
});

it('does not increment view count on repeat visit in same session', function () {
    $info = Information::factory()->create(['is_published' => true, 'views' => 0]);

    $this->get(route('information.show', $info));
    $this->get(route('information.show', $info));

    expect($info->fresh()->views)->toBe(1);
});

it('increments view count for different articles independently', function () {
    $info1 = Information::factory()->create(['is_published' => true, 'views' => 0]);
    $info2 = Information::factory()->create(['is_published' => true, 'views' => 0]);

    $this->get(route('information.show', $info1));
    $this->get(route('information.show', $info2));

    expect($info1->fresh()->views)->toBe(1);
    expect($info2->fresh()->views)->toBe(1);
});
