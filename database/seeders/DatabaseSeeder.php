<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        City::firstOrCreate(['slug' => 'samarinda'], ['name' => 'Samarinda']);
        $this->call(BadgeSeeder::class);
    }
}
