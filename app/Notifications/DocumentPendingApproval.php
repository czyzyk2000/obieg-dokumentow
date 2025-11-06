<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * DocumentPendingApproval Notification
 * 
 * Sent to managers/finance when a document requires their approval.
 * Queued for async delivery.
 */
class DocumentPendingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Document $document
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nowy dokument do akceptacji')
            ->greeting("Witaj {$notifiable->name}!")
            ->line("Dokument \"{$this->document->title}\" czeka na Twoją akceptację.")
            ->line("Kwota: {$this->document->formatted_amount}")
            ->line("Utworzony przez: {$this->document->user->name}")
            ->action('Zobacz dokument', url("/documents/{$this->document->id}"))
            ->line('Dziękujemy za szybką reakcję!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'document_amount' => $this->document->amount,
            'document_status' => $this->document->status->value,
            'created_by' => $this->document->user->name,
            'message' => "Dokument \"{$this->document->title}\" czeka na akceptację",
        ];
    }
}
