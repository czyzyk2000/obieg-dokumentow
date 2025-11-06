<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * DocumentWorkflowService
 * 
 * Handles document state transitions and approval workflow.
 * Implements state machine logic with transaction safety.
 */
class DocumentWorkflowService
{
    /**
     * Submit document for manager approval
     */
    public function submit(Document $document, User $user): void
    {
        DB::transaction(function () use ($document) {
            $document->submit();
        });
    }

    /**
     * Approve document as manager
     */
    public function approveByManager(Document $document, User $manager, ?string $comment = null): void
    {
        DB::transaction(function () use ($document, $manager, $comment) {
            $document->approveByManager($manager, $comment);
        });
    }

    /**
     * Reject document as manager
     */
    public function rejectByManager(Document $document, User $manager, string $comment): void
    {
        DB::transaction(function () use ($document, $manager, $comment) {
            $document->rejectByManager($manager, $comment);
        });
    }

    /**
     * Approve document as finance
     */
    public function approveByFinance(Document $document, User $finance, ?string $comment = null): void
    {
        DB::transaction(function () use ($document, $finance, $comment) {
            $document->approveByFinance($finance, $comment);
        });
    }

    /**
     * Reject document as finance
     */
    public function rejectByFinance(Document $document, User $finance, string $comment): void
    {
        DB::transaction(function () use ($document, $finance, $comment) {
            $document->rejectByFinance($finance, $comment);
        });
    }
}
