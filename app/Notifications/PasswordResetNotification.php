<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $newPassword)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Kata Sandi Akun Anda')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kata sandi akun Antropometri Anda telah diatur ulang.')
            ->line('Berikut adalah kata sandi baru Anda:')
            ->line('**' . $this->newPassword . '**')
            ->line('Silakan login menggunakan kata sandi ini dan segera ubah melalui menu Profil.')
            ->action('Login ke Aplikasi', url('/'))
            ->line('Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
