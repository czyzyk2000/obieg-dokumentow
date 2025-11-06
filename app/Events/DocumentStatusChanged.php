<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * DocumentStatusChanged Event
 * 
 * Fired whenever a document's status changes.
 * Triggers audit logging and notifications.
 */
class DocumentStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Document $document,
        public DocumentStatus $oldStatus,
        public DocumentStatus $newStatus,
        public User $actor,
        public string $action,
        public ?string $comment = null
    ) {}
}
