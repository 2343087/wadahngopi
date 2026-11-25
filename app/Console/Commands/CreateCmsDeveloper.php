<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateCmsDeveloper extends Command
{
    protected $signature = 'wadahngopi:developer';

    protected $description = 'Create a developer account for the WadahNgopi CMS';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Jalankan perintah ini di terminal interaktif.');

            return self::FAILURE;
        }

        $data = [
            'name' => trim((string) $this->ask('Nama')),
            'email' => Str::lower(trim((string) $this->ask('Email'))),
            'password' => $this->secret('Kata sandi'),
            'password_confirmation' => $this->secret('Ulangi kata sandi'),
        ];
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'string', 'min:12', 'confirmed', fn ($attribute, $value, $fail) => strlen($value) > 72 && $fail('Kata sandi maksimal 72 byte.')],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create($validator->validated() + ['role' => UserRole::Developer->value]);
        $this->info('Akun developer dibuat. Masuk melalui /admin/login.');

        return self::SUCCESS;
    }
}
