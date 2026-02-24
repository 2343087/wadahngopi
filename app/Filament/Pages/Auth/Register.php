<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';

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
            ->required();
    }
}
