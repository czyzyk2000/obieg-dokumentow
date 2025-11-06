<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * DocumentApproved Notification
 * 
 * Sent to document owner when their document is approved.
 */
class DocumentApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
        public User $approver
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Dokument zaakceptowany')
            ->greeting("Witaj {$notifiable->name}!")
            ->line("Twój dokument \"{$this->document->title}\" został zaakceptowany.")
            ->line("Zaakceptował: {$this->approver->name}")
            ->line("Kwota: {$this->document->formatted_amount}")
            ->action('Zobacz dokument', url("/documents/{$this->document->id}"))
            ->line('Gratulacje!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'approver_name' => $this->approver->name,
            'message' => "Dokument \"{$this->document->title}\" został zaakceptowany",
        ];
    }
}
