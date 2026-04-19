<?php

namespace App\Notifications;

use App\Models\PermohonanToner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TonerDiluluskan extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly PermohonanToner $permohonan,
        public readonly string $catatan = '',
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->permohonan;
        $mail = (new MailMessage)
            ->subject("[ICTServe] Permohonan Toner Diluluskan — #{$p->no_tiket}")
            ->greeting("Salam {$notifiable->name},")
            ->line('Permohonan toner anda telah **DILULUSKAN**.')
            ->line("**No. Tiket:** #{$p->no_tiket}")
            ->line("**Kuantiti Diminta:** {$p->kuantiti} unit")
            ->line("**Kuantiti Diluluskan:** {$p->kuantiti_diluluskan} unit");

        if ($this->catatan) {
            $mail->line("**Catatan Pentadbir:** {$this->catatan}");
        }

        return $mail
            ->line('Toner akan diserahkan kepada anda tidak lama lagi.')
            ->action('Semak Status', route('m02.senarai'))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
