<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cafe>
 */
class CafeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Coffee',
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'google_maps_url' => 'https://maps.google.com/?q='.urlencode(fake()->address()),
            'whatsapp_number' => fake()->phoneNumber(),
            'has_wifi' => fake()->boolean(),
            'rating' => fake()->randomFloat(2, 3, 5),
            'image_path' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&q=80&w=800',
            'status' => 'published',
            'total_energy' => 0,
        ];
    }
}
