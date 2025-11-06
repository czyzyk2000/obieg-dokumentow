<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\DocumentStatus;
use App\Enums\UserRole;
use App\Models\Document;
use App\Models\User;

/**
 * DocumentPolicy
 * 
 * Authorization logic for document operations.
 * Implements fine-grained access control based on roles and document status.
 */
class DocumentPolicy
{
    /**
     * Determine if user can view any documents
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can see documents list
    }

    /**
     * Determine if user can view the document
     */
    public function view(User $user, Document $document): bool
    {
        return $document->user_id === $user->id
            || $document->user->manager_id === $user->id
            || in_array($user->role, [UserRole::FINANCE, UserRole::ADMIN]);
    }

    /**
     * Determine if user can create documents
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create documents
    }

    /**
     * Determine if user can update the document
     */
    public function update(User $user, Document $document): bool
    {
        return $document->user_id === $user->id
            && $document->status === DocumentStatus::DRAFT;
    }

    /**
     * Determine if user can delete the document
     */
    public function delete(User $user, Document $document): bool
    {
        return $document->user_id === $user->id
            && $document->status === DocumentStatus::DRAFT;
    }

    /**
     * Determine if user can submit document for approval
     */
    public function submit(User $user, Document $document): bool
    {
        return $document->user_id === $user->id
            && $document->status === DocumentStatus::DRAFT
            && $user->hasManager();
    }

    /**
     * Determine if user can approve document as manager
     */
    public function approveAsManager(User $user, Document $document): bool
    {
        return $user->role === UserRole::MANAGER
            && $document->user->manager_id === $user->id
            && $document->status === DocumentStatus::PENDING_MANAGER_APPROVAL;
    }

    /**
     * Determine if user can reject document as manager
     */
    public function rejectAsManager(User $user, Document $document): bool
    {
        return $this->approveAsManager($user, $document);
    }

    /**
     * Determine if user can approve document as finance
     */
    public function approveAsFinance(User $user, Document $document): bool
    {
        return $user->role === UserRole::FINANCE
            && $document->status === DocumentStatus::PENDING_FINANCE_APPROVAL;
    }

    /**
     * Determine if user can reject document as finance
     */
    public function rejectAsFinance(User $user, Document $document): bool
    {
        return $this->approveAsFinance($user, $document);
    }

    /**
     * Determine if user can download document file
     */
    public function downloadFile(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}
