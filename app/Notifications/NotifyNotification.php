<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyNotification extends Notification
{
    use Queueable;
    private $tag;
    private $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $tag, string $message)
    {
        $this->tag = $tag;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tag' => $this->tag,
            'message' => $this->message,
        ];
    }
}
