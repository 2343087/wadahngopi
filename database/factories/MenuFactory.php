<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cafe_id' => \App\Models\Cafe::factory(),
            'name' => fake()->word().' Kopi',
            'price' => fake()->numberBetween(15000, 50000),
            'type' => fake()->randomElement(['coffee', 'non-coffee', 'food']),
            'image_path' => 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800',
        ];
    }
}
