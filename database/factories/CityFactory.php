<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kaltimCities = [
            'Samarinda',
            'Balikpapan',
            'Bontang',
            'Sangatta',
            'Tenggarong',
            'Tanjung Redeb',
            'Sendawar',
            'Tanah Grogot',
            'Penajam',
            'Ujoh Bilang',
        ];

        $name = fake()->randomElement($kaltimCities).' '.Str::random(5);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
        ];
    }
}
