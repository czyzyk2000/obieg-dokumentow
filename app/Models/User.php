<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * 
 * Represents a user in the system with role-based permissions
 * and hierarchical manager-subordinate relationships.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'manager_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the manager of this user
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all subordinates (users who report to this user)
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Get all documents created by this user
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all document history actions performed by this user
     */
    public function documentActions(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to get only managers
     */
    public function scopeManagers($query)
    {
        return $query->where('role', UserRole::MANAGER);
    }

    /**
     * Scope to get only finance users
     */
    public function scopeFinance($query)
    {
        return $query->where('role', UserRole::FINANCE);
    }

    /**
     * Scope to get only admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::ADMIN);
    }

    /**
     * Scope to get regular users
     */
    public function scopeRegularUsers($query)
    {
        return $query->where('role', UserRole::USER);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if user is a manager
     */
    public function isManager(): bool
    {
        return $this->role === UserRole::MANAGER;
    }

    /**
     * Check if user is in finance department
     */
    public function isFinance(): bool
    {
        return $this->role === UserRole::FINANCE;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user is a regular user
     */
    public function isRegularUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    /**
     * Check if user can approve documents
     */
    public function canApproveDocuments(): bool
    {
        return $this->role->canApproveDocuments();
    }

    /**
     * Check if user has subordinates
     */
    public function hasSubordinates(): bool
    {
        return $this->subordinates()->exists();
    }

    /**
     * Check if user has a manager
     */
    public function hasManager(): bool
    {
        return $this->manager_id !== null;
    }

    /**
     * Get user's full role label
     */
    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    /**
     * Check if this user is manager of given user
     */
    public function isManagerOf(User $user): bool
    {
        return $this->id === $user->manager_id;
    }
}
