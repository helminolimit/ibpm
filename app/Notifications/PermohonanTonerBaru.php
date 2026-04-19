<?php

namespace App\Notifications;

use App\Models\PermohonanToner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermohonanTonerBaru extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly PermohonanToner $permohonan) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->permohonan;

        return (new MailMessage)
            ->subject("[ICTServe] Permohonan Toner Baru — #{$p->no_tiket}")
            ->greeting('Pentadbir BPM,')
            ->line('Permohonan toner baharu telah diterima.')
            ->line("**No. Tiket:** #{$p->no_tiket}")
            ->line("**Pemohon:** {$p->user->name}")
            ->line("**Bahagian:** {$p->user->bahagian}")
            ->line("**Model Pencetak:** {$p->model_pencetak}")
            ->line("**Jenis Toner:** {$p->jenis_toner->label()}")
            ->line("**Kuantiti:** {$p->kuantiti} unit")
            ->salutation('ICTServe');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
