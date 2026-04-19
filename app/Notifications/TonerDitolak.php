<?php

namespace App\Notifications;

use App\Models\PermohonanToner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TonerDitolak extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly PermohonanToner $permohonan,
        public readonly string $sebabPenolakan,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->permohonan;

        return (new MailMessage)
            ->subject("[ICTServe] Permohonan Toner Ditolak — #{$p->no_tiket}")
            ->greeting("Salam {$notifiable->name},")
            ->line('Maaf, permohonan toner anda telah **DITOLAK**.')
            ->line("**No. Tiket:** #{$p->no_tiket}")
            ->line("**Sebab Penolakan:** {$this->sebabPenolakan}")
            ->line('Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna untuk maklumat lanjut atau hantar permohonan baharu.')
            ->action('Hantar Permohonan Baru', route('m02.permohonan-baru'))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
