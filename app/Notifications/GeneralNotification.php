<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected string $title;

    protected string $message;

    protected ?string $type;

    protected ?string $actionUrl;

    public function __construct(string $title, string $message, ?string $type = 'general', ?string $actionUrl = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->actionUrl = $actionUrl;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->message,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->message,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }
}
