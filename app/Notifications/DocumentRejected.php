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
 * DocumentRejected Notification
 * 
 * Sent to document owner when their document is rejected.
 */
class DocumentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
        public User $rejector,
        public ?string $comment
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Dokument odrzucony')
            ->greeting("Witaj {$notifiable->name}!")
            ->line("Twój dokument \"{$this->document->title}\" został odrzucony.")
            ->line("Odrzucił: {$this->rejector->name}");

        if ($this->comment) {
            $mail->line("Powód: {$this->comment}");
        }

        return $mail
            ->action('Zobacz dokument', url("/documents/{$this->document->id}"))
            ->line('Możesz utworzyć nowy dokument.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'rejector_name' => $this->rejector->name,
            'comment' => $this->comment,
            'message' => "Dokument \"{$this->document->title}\" został odrzucony",
        ];
    }
}
