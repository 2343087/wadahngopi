<?php

use App\Models\Cafe;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->city = City::create(['name' => 'Banjarmasin', 'slug' => 'banjarmasin']);
    $this->user = User::factory()->create(['role' => 'admin']);
});

it('identifies a 24-hour cafe as always open', function () {
    $cafe = Cafe::factory()->create([
        'city_id' => $this->city->id,
        'owner_id' => $this->user->id,
        'is_24_hours' => true,
        'latitude' => -3.316694,
        'longitude' => 114.590111,
    ]);

    expect($cafe->is_open)->toBeTrue();

    // Check at late night
    Carbon::setTestNow(now()->setTime(23, 59));
    expect($cafe->is_open)->toBeTrue();
});

it('correctly handles weekday vs weekend hours', function () {
    $cafe = Cafe::factory()->create([
        'city_id' => $this->city->id,
        'owner_id' => $this->user->id,
        'operating_hours' => [
            'weekday' => ['open' => '08:00', 'close' => '20:00'],
            'weekend' => ['open' => '10:00', 'close' => '23:00'],
        ],
        'latitude' => -3.316694,
        'longitude' => 114.590111,
    ]);

    // Monday (Weekday) at 9:00 AM - Should be open
    Carbon::setTestNow(Carbon::parse('next monday')->setTime(9, 0));
    expect($cafe->is_open)->toBeTrue();

    // Monday at 9:00 PM - Should be closed (weekday closes at 20:00)
    Carbon::setTestNow(Carbon::parse('next monday')->setTime(21, 0));
    expect($cafe->is_open)->toBeFalse();

    // Saturday (Weekend) at 9:00 PM - Should be open (weekend closes at 23:00)
    Carbon::setTestNow(Carbon::parse('next saturday')->setTime(21, 0));
    expect($cafe->is_open)->toBeTrue();
});

it('handles overnight hours in flexible schedule', function () {
    $cafe = Cafe::factory()->create([
        'city_id' => $this->city->id,
        'owner_id' => $this->user->id,
        'operating_hours' => [
            'weekday' => ['open' => '18:00', 'close' => '02:00'],
            'weekend' => ['open' => '18:00', 'close' => '04:00'],
        ],
        'latitude' => -3.316694,
        'longitude' => 114.590111,
    ]);

    // Tuesday at 1:00 AM - Should be open (weekday closes at 02:00)
    Carbon::setTestNow(Carbon::parse('next tuesday')->setTime(1, 0));
    expect($cafe->is_open)->toBeTrue();

    // Tuesday at 3:00 AM - Should be closed (weekday closes at 02:00)
    Carbon::setTestNow(Carbon::parse('next tuesday')->setTime(3, 0));
    expect($cafe->is_open)->toBeFalse();
});

it('falls back to legacy fields if operating_hours is null', function () {
    $cafe = Cafe::factory()->create([
        'city_id' => $this->city->id,
        'owner_id' => $this->user->id,
        'opening_time' => '08:00',
        'closing_time' => '17:00',
        'latitude' => -3.316694,
        'longitude' => 114.590111,
    ]);

    Carbon::setTestNow(now()->setTime(10, 0));
    expect($cafe->is_open)->toBeTrue();

    Carbon::setTestNow(now()->setTime(18, 0));
    expect($cafe->is_open)->toBeFalse();
});
