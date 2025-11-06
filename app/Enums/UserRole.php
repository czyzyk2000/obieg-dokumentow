<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User Role Enum
 * 
 * Defines all possible user roles in the system with their permissions.
 * Uses PHP 8.1+ backed enums for type safety and better IDE support.
 */
enum UserRole: string
{
    case USER = 'user';
    case MANAGER = 'manager';
    case FINANCE = 'finance';
    case ADMIN = 'admin';

    /**
     * Get human-readable label for the role
     */
    public function label(): string
    {
        return match ($this) {
            self::USER => 'Pracownik',
            self::MANAGER => 'Menedżer',
            self::FINANCE => 'Dział Finansowy',
            self::ADMIN => 'Administrator',
        };
    }

    /**
     * Get role description
     */
    public function description(): string
    {
        return match ($this) {
            self::USER => 'Zwykły pracownik - może tworzyć i edytować własne wnioski',
            self::MANAGER => 'Menedżer - może akceptować wnioski swoich podwładnych',
            self::FINANCE => 'Dział finansowy - może akceptować wnioski o wartości >= 1000 PLN',
            self::ADMIN => 'Administrator - pełny dostęp do systemu',
        };
    }

    /**
     * Check if role can approve documents
     */
    public function canApproveDocuments(): bool
    {
        return in_array($this, [self::MANAGER, self::FINANCE, self::ADMIN]);
    }

    /**
     * Check if role can manage users
     */
    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Get all roles as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get roles for select dropdown
     */
    public static function forSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($role) => [$role->value => $role->label()])
            ->toArray();
    }
}
