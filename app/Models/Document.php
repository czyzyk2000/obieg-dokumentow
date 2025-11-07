<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Events\DocumentStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Document Model
 * 
 * Represents a purchase request document with state machine workflow.
 * Implements business logic for document approval process.
 */
class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'amount',
        'status',
        'file_path',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'draft',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => DocumentStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user who created this document
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all history records for this document
     */
    public function history(): HasMany
    {
        return $this->hasMany(DocumentHistory::class)->latest();
    }

    // ==================== SCOPES ====================

    /**
     * Scope to get only draft documents
     */
    public function scopeDraft($query)
    {
        return $query->where('status', DocumentStatus::DRAFT);
    }

    /**
     * Scope to get documents pending manager approval
     */
    public function scopePendingManagerApproval($query)
    {
        return $query->where('status', DocumentStatus::PENDING_MANAGER_APPROVAL);
    }

    /**
     * Scope to get documents pending finance approval
     */
    public function scopePendingFinanceApproval($query)
    {
        return $query->where('status', DocumentStatus::PENDING_FINANCE_APPROVAL);
    }

    /**
     * Scope to get approved documents
     */
    public function scopeApproved($query)
    {
        return $query->where('status', DocumentStatus::APPROVED);
    }

    /**
     * Scope to get rejected documents
     */
    public function scopeRejected($query)
    {
        return $query->where('status', DocumentStatus::REJECTED);
    }

    /**
     * Scope to get documents for specific manager
     * (documents created by manager's subordinates)
     */
    public function scopeForManager($query, User $manager)
    {
        return $query->whereHas('user', function ($q) use ($manager) {
            $q->where('manager_id', $manager->id);
        });
    }

    /**
     * Scope to get documents for finance department
     */
    public function scopeForFinance($query)
    {
        return $query->where('status', DocumentStatus::PENDING_FINANCE_APPROVAL);
    }

    // ==================== STATUS CHECKS ====================

    /**
     * Check if document is in draft status
     */
    public function isDraft(): bool
    {
        return $this->status === DocumentStatus::DRAFT;
    }

    /**
     * Check if document is pending any approval
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    /**
     * Check if document is approved
     */
    public function isApproved(): bool
    {
        return $this->status === DocumentStatus::APPROVED;
    }

    /**
     * Check if document is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === DocumentStatus::REJECTED;
    }

    /**
     * Check if document can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status->canBeEdited();
    }

    /**
     * Check if document is in final state
     */
    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    /**
     * Check if document requires finance approval
     */
    public function requiresFinanceApproval(): bool
    {
        return $this->amount >= 1000;
    }

    // ==================== STATE MACHINE METHODS ====================

    /**
     * Submit document for approval
     */
    public function submit(): void
    {
        $this->transitionTo(
            DocumentStatus::PENDING_MANAGER_APPROVAL,
            auth()->user(),
            'submitted'
        );
    }

    /**
     * Approve document by manager
     */
    public function approveByManager(User $manager, ?string $comment = null): void
    {
        $newStatus = $this->requiresFinanceApproval()
            ? DocumentStatus::PENDING_FINANCE_APPROVAL
            : DocumentStatus::APPROVED;

        $action = $this->requiresFinanceApproval()
            ? 'approved_by_manager'
            : 'approved';

        $this->transitionTo($newStatus, $manager, $action, $comment);
    }

    /**
     * Reject document by manager
     */
    public function rejectByManager(User $manager, string $comment): void
    {
        $this->transitionTo(
            DocumentStatus::REJECTED,
            $manager,
            'rejected_by_manager',
            $comment
        );
    }

    /**
     * Approve document by finance
     */
    public function approveByFinance(User $finance, ?string $comment = null): void
    {
        $this->transitionTo(
            DocumentStatus::APPROVED,
            $finance,
            'approved_by_finance',
            $comment
        );
    }

    /**
     * Reject document by finance
     */
    public function rejectByFinance(User $finance, string $comment): void
    {
        $this->transitionTo(
            DocumentStatus::REJECTED,
            $finance,
            'rejected_by_finance',
            $comment
        );
    }

    /**
     * Internal method to handle status transitions
     */
    protected function transitionTo(
        DocumentStatus $newStatus,
        User $actor,
        string $action,
        ?string $comment = null
    ): void {
        $oldStatus = $this->status;

        if (!$oldStatus->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$oldStatus->value} to {$newStatus->value}"
            );
        }

        $this->status = $newStatus;
        $this->save();

        event(new DocumentStatusChanged(
            $this,
            $oldStatus,
            $newStatus,
            $actor,
            $action,
            $comment
        ));
    }

    // ==================== FILE HANDLING ====================

    /**
     * Check if document has attached file
     */
    public function hasFile(): bool
    {
        return $this->file_path !== null && Storage::disk('private_documents')->exists($this->file_path);
    }

    /**
     * Get file size in human-readable format
     */
    public function getFileSizeAttribute(): ?string
    {
        if (!$this->hasFile()) {
            return null;
        }

        $bytes = Storage::disk('private_documents')->size($this->file_path);
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file name from path
     */
    public function getFileNameAttribute(): ?string
    {
        return $this->file_path ? basename($this->file_path) : null;
    }

    /**
     * Delete attached file
     */
    public function deleteFile(): void
    {
        if ($this->hasFile()) {
            Storage::disk('private_documents')->delete($this->file_path);
            $this->file_path = null;
            $this->save();
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2, ',', ' ') . ' PLN';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status->badgeColor();
        $label = $this->status->label();

        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
