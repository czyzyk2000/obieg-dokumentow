<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Document Status Enum
 * 
 * Represents the state machine for document workflow.
 * Each status has specific transitions and business rules.
 */
enum DocumentStatus: string
{
    case DRAFT = 'draft';
    case PENDING_MANAGER_APPROVAL = 'pending_manager_approval';
    case PENDING_FINANCE_APPROVAL = 'pending_finance_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get human-readable label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Szkic',
            self::PENDING_MANAGER_APPROVAL => 'Oczekuje na akceptację menedżera',
            self::PENDING_FINANCE_APPROVAL => 'Oczekuje na akceptację finansową',
            self::APPROVED => 'Zaakceptowany',
            self::REJECTED => 'Odrzucony',
        };
    }

    /**
     * Get status badge color for UI
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_MANAGER_APPROVAL => 'yellow',
            self::PENDING_FINANCE_APPROVAL => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }

    /**
     * Get status icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'document-text',
            self::PENDING_MANAGER_APPROVAL => 'clock',
            self::PENDING_FINANCE_APPROVAL => 'currency-dollar',
            self::APPROVED => 'check-circle',
            self::REJECTED => 'x-circle',
        };
    }

    /**
     * Check if document can be edited
     */
    public function canBeEdited(): bool
    {
        return $this === self::DRAFT;
    }

    /**
     * Check if document is pending approval
     */
    public function isPending(): bool
    {
        return in_array($this, [
            self::PENDING_MANAGER_APPROVAL,
            self::PENDING_FINANCE_APPROVAL,
        ]);
    }

    /**
     * Check if document is in final state
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    /**
     * Get allowed transitions from current status
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::PENDING_MANAGER_APPROVAL],
            self::PENDING_MANAGER_APPROVAL => [
                self::APPROVED,
                self::PENDING_FINANCE_APPROVAL,
                self::REJECTED,
            ],
            self::PENDING_FINANCE_APPROVAL => [
                self::APPROVED,
                self::REJECTED,
            ],
            self::APPROVED, self::REJECTED => [],
        };
    }

    /**
     * Check if transition to new status is allowed
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }

    /**
     * Get all statuses as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get statuses for select dropdown
     */
    public static function forSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }
}
