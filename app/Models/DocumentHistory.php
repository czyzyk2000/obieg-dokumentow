<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DocumentHistory Model
 * 
 * Audit trail for all document actions.
 * Records who did what and when for compliance and transparency.
 */
class DocumentHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'user_id',
        'action',
        'comment',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ==================== ACTION CONSTANTS ====================

    public const ACTION_CREATED = 'created';
    public const ACTION_SUBMITTED = 'submitted';
    public const ACTION_APPROVED_BY_MANAGER = 'approved_by_manager';
    public const ACTION_REJECTED_BY_MANAGER = 'rejected_by_manager';
    public const ACTION_APPROVED_BY_FINANCE = 'approved_by_finance';
    public const ACTION_REJECTED_BY_FINANCE = 'rejected_by_finance';
    public const ACTION_APPROVED = 'approved';
    public const ACTION_REJECTED = 'rejected';

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the document this history entry belongs to
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who performed this action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get human-readable action label
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'Utworzono dokument',
            self::ACTION_SUBMITTED => 'Wysłano do akceptacji',
            self::ACTION_APPROVED_BY_MANAGER => 'Zaakceptowano przez menedżera',
            self::ACTION_REJECTED_BY_MANAGER => 'Odrzucono przez menedżera',
            self::ACTION_APPROVED_BY_FINANCE => 'Zaakceptowano przez dział finansowy',
            self::ACTION_REJECTED_BY_FINANCE => 'Odrzucono przez dział finansowy',
            self::ACTION_APPROVED => 'Zaakceptowano',
            self::ACTION_REJECTED => 'Odrzucono',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get action icon for UI
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'document-plus',
            self::ACTION_SUBMITTED => 'paper-airplane',
            self::ACTION_APPROVED_BY_MANAGER,
            self::ACTION_APPROVED_BY_FINANCE,
            self::ACTION_APPROVED => 'check-circle',
            self::ACTION_REJECTED_BY_MANAGER,
            self::ACTION_REJECTED_BY_FINANCE,
            self::ACTION_REJECTED => 'x-circle',
            default => 'information-circle',
        };
    }

    /**
     * Get action color for UI
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'blue',
            self::ACTION_SUBMITTED => 'yellow',
            self::ACTION_APPROVED_BY_MANAGER,
            self::ACTION_APPROVED_BY_FINANCE,
            self::ACTION_APPROVED => 'green',
            self::ACTION_REJECTED_BY_MANAGER,
            self::ACTION_REJECTED_BY_FINANCE,
            self::ACTION_REJECTED => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if action is an approval
     */
    public function isApproval(): bool
    {
        return in_array($this->action, [
            self::ACTION_APPROVED_BY_MANAGER,
            self::ACTION_APPROVED_BY_FINANCE,
            self::ACTION_APPROVED,
        ]);
    }

    /**
     * Check if action is a rejection
     */
    public function isRejection(): bool
    {
        return in_array($this->action, [
            self::ACTION_REJECTED_BY_MANAGER,
            self::ACTION_REJECTED_BY_FINANCE,
            self::ACTION_REJECTED,
        ]);
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    /**
     * Get relative time (e.g., "2 hours ago")
     */
    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
