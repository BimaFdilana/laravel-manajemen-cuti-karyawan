<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $link;
    protected $icon;

    public function __construct(string $title, string $message, string $link = '#', string $icon = 'fas fa-bell')
    {
        $this->title = $title;
        $this->message = $message;
        $this->link = $link;
        $this->icon = $icon;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'link' => $this->link,
            'icon' => $this->icon,
        ];
    }
}