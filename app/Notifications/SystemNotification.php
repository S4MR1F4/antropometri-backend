<?php

namespace App\Notifications;

use App\Support\MobileLink;
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
        $channels = ['database'];

        // Only send email for critical clinical alerts or when explicitly flagged,
        // preventing email inbox spam on routine normal records.
        if (!empty($this->meta['is_critical']) || !empty($this->meta['send_mail'])) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $actionUrl = isset($this->meta['card_url'])
            ? $this->meta['card_url']
            : (isset($this->meta['measurement_id'])
                ? MobileLink::url('/measurements/' . $this->meta['measurement_id'], [
                    'subject_id' => $this->meta['subject_id'] ?? null,
                ])
                : MobileLink::url('/notifications'));

        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Yth. ' . $notifiable->name . ',');

        if (!empty($this->meta['is_critical'])) {
            $mail->error();
            $mail->line('PERINGATAN KLINIS: Sistem mendeteksi hasil antropometri yang memerlukan perhatian atau intervensi.');
        }

        $mail->line($this->message);

        if (!empty($this->meta['details']) && is_array($this->meta['details'])) {
            foreach ($this->meta['details'] as $label => $value) {
                if ($value !== null && $value !== '') {
                    $mail->line("{$label}: {$value}");
                }
            }
        }

        if (!empty($this->meta['recommendation'])) {
            $mail->line('Rekomendasi Tindakan: ' . $this->meta['recommendation']);
        }

        return $mail
            ->action('Lihat Kartu Pasien & Riwayat Lengkap', $actionUrl)
            ->line('Pemberitahuan resmi dari Sistem Informasi Antropometri & Status Gizi Nasional.');
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
