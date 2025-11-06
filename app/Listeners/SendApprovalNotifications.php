<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\DocumentStatus;
use App\Events\DocumentStatusChanged;
use App\Models\User;
use App\Notifications\DocumentApproved;
use App\Notifications\DocumentPendingApproval;
use App\Notifications\DocumentRejected;

/**
 * SendApprovalNotifications Listener
 * 
 * Sends appropriate notifications based on document status changes.
 * Uses strategy pattern to route notifications to correct recipients.
 */
class SendApprovalNotifications
{
    /**
     * Handle the event.
     */
    public function handle(DocumentStatusChanged $event): void
    {
        match ($event->newStatus) {
            DocumentStatus::PENDING_MANAGER_APPROVAL => $this->notifyManager($event),
            DocumentStatus::PENDING_FINANCE_APPROVAL => $this->notifyFinance($event),
            DocumentStatus::APPROVED => $this->notifyOwnerApproved($event),
            DocumentStatus::REJECTED => $this->notifyOwnerRejected($event),
            default => null,
        };
    }

    /**
     * Notify manager when document is submitted
     */
    protected function notifyManager(DocumentStatusChanged $event): void
    {
        $manager = $event->document->user->manager;

        if ($manager) {
            $manager->notify(new DocumentPendingApproval($event->document));
        }
    }

    /**
     * Notify finance department when document requires finance approval
     */
    protected function notifyFinance(DocumentStatusChanged $event): void
    {
        $financeUsers = User::finance()->get();

        foreach ($financeUsers as $financeUser) {
            $financeUser->notify(new DocumentPendingApproval($event->document));
        }
    }

    /**
     * Notify document owner when document is approved
     */
    protected function notifyOwnerApproved(DocumentStatusChanged $event): void
    {
        $event->document->user->notify(
            new DocumentApproved($event->document, $event->actor)
        );
    }

    /**
     * Notify document owner when document is rejected
     */
    protected function notifyOwnerRejected(DocumentStatusChanged $event): void
    {
        $event->document->user->notify(
            new DocumentRejected($event->document, $event->actor, $event->comment)
        );
    }
}
