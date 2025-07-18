<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CutiNotification extends Notification
{
    use Queueable;

    protected $cuti;
    protected $message;

    public function __construct(Cuti $cuti, string $message)
    {
        $this->cuti = $cuti;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan Cuti Baru',
            'message' => $this->message,
            'cuti_id' => $this->cuti->id,
            'karyawan_nama' => $this->cuti->karyawan->nama_karyawan ?? 'N/A',
        ];
    }
}