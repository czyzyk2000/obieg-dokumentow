<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DocumentStatusChanged;
use App\Models\DocumentHistory;

/**
 * LogDocumentHistory Listener
 * 
 * Listens to DocumentStatusChanged events and creates audit trail records.
 */
class LogDocumentHistory
{
    /**
     * Handle the event.
     */
    public function handle(DocumentStatusChanged $event): void
    {
        DocumentHistory::create([
            'document_id' => $event->document->id,
            'user_id' => $event->actor->id,
            'action' => $event->action,
            'comment' => $event->comment,
        ]);
    }
}
