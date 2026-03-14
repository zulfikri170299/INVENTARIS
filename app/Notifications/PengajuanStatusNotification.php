<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanStatusNotification extends Notification
{
    use Queueable;

    protected $pengajuan;
    protected $title;
    protected $message;

    public function __construct($pengajuan, $title, $message)
    {
        $this->pengajuan = $pengajuan;
        $this->title = $title;
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
            'pengajuan_id' => $this->pengajuan->id,
            'judul' => $this->pengajuan->judul,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->pengajuan->status,
        ];
    }
}
