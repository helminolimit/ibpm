<?php

namespace App\Notifications;

use App\Models\PermohonanToner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TonerDihantar extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly PermohonanToner $permohonan,
        public readonly int $kuantitiDihantar = 0,
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
            ->subject("[ICTServe] Toner Telah Dihantar — #{$p->no_tiket}")
            ->greeting("Salam {$notifiable->name},")
            ->line('Toner untuk permohonan anda telah berjaya dihantar.')
            ->line("**No. Tiket:** #{$p->no_tiket}")
            ->line("**Kuantiti Dihantar:** {$this->kuantitiDihantar} unit")
            ->line('**Tarikh Dihantar:** '.now()->format('d M Y'))
            ->line('Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna sekiranya terdapat sebarang pertanyaan.')
            ->action('Lihat Butiran', route('m02.butiran', $p->id))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
