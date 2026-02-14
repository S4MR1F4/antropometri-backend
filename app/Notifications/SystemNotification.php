<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $meta;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $meta = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->meta = $meta;
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
            ->subject($this->title)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line($this->message)
            ->action('Buka Aplikasi', url(config('app.url')))
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'measurement_id' => $this->meta['measurement_id'] ?? null,
            'subject_id' => $this->meta['subject_id'] ?? null,
            'subject_name' => $this->meta['subject_name'] ?? null,
        ];
    }
}
