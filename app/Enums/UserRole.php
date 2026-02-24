<?php

namespace App\Enums;

enum UserRole: string
{
    case Developer = 'developer';
    case Admin = 'admin';
    case Roastery = 'roastery';
    case User = 'user';

    /**
     * Get all valid role values as an array.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the role can access the Filament admin panel.
     */
    public function canAccessPanel(): bool
    {
        return in_array($this, [self::Developer, self::Admin, self::Roastery]);
    }

    /**
     * Check if the role is a super admin (developer).
     */
    public function isDeveloper(): bool
    {
        return $this === self::Developer;
    }

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Developer => 'Developer',
            self::Admin => 'Cafe Owner',
            self::Roastery => 'Roastery Owner',
            self::User => 'User',
        };
    }
}
