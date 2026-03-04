<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';

    /**
     * Allowed roles for public registration.
     * 'developer' is INTENTIONALLY excluded — only assignable via database/tinker.
     */
    private const ALLOWED_ROLES = ['admin', 'roastery'];

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                    $this->getRoleFormComponent(),
                ])
                ->statePath('data'),
        ];
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('role')
            ->label('Daftar Sebagai')
            ->options([
                'admin' => 'Owner Cafe',
                'roastery' => 'Owner Roastery',
            ])
            ->default('admin')
            ->required()
            ->in(self::ALLOWED_ROLES);
    }

    /**
     * Override to enforce role whitelist at the database level.
     */
    protected function handleRegistration(array $data): Model
    {
        // Double-check: even if someone bypasses frontend, block unauthorized roles
        if (! in_array($data['role'] ?? '', self::ALLOWED_ROLES, true)) {
            $data['role'] = 'admin'; // Default fallback
        }

        return parent::handleRegistration($data);
    }
}
