<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('TestAccountsSeeder dilewati di environment production.');

            return;
        }

        $accounts = [
            [
                'email' => 'dev@wadahngopi.test',
                'name' => 'Tim Developer',
                'role' => UserRole::Developer->value,
            ],
            [
                'email' => 'owner.cafe@wadahngopi.test',
                'name' => 'Tim Owner Cafe',
                'role' => UserRole::Admin->value,
            ],
            [
                'email' => 'owner.roastery@wadahngopi.test',
                'name' => 'Tim Owner Roastery',
                'role' => UserRole::Roastery->value,
            ],
            [
                'email' => 'user@wadahngopi.test',
                'name' => 'Tim Pengunjung',
                'role' => UserRole::User->value,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('Password123!'),
                    'role' => $account['role'],
                ]
            );
        }

        $this->command?->info('4 akun uji coba untuk semua role berhasil disiapkan.');
    }
}
