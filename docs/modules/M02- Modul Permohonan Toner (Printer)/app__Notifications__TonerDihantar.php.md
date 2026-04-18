# `app/Notifications/TonerDihantar.php`

```php
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

    public function __construct(public readonly PermohonanToner $permohonan) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $penghantaran = $this->permohonan->penghantaran()->latest()->first();

        return (new MailMessage)
            ->subject("[ICTServe] Toner Telah Dihantar — {$this->permohonan->no_tiket}")
            ->greeting('Salam hormat,')
            ->line('Toner untuk permohonan anda telah **berjaya dihantar**.')
            ->line("**No. Tiket:** {$this->permohonan->no_tiket}")
            ->line("**Kuantiti Dihantar:** {$penghantaran?->kuantiti_dihantar} unit")
            ->line("**Tarikh Dihantar:** " . now()->format('d M Y'))
            ->line('Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna sekiranya ada pertanyaan.')
            ->action('Lihat Butiran', route('m02.butiran', $this->permohonan->no_tiket))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'no_tiket'      => $this->permohonan->no_tiket,
            'jenis'         => 'toner_dihantar',
            'mesej'         => "Toner {$this->permohonan->no_tiket} telah dihantar.",
        ];
    }
}
```
