# `app/Notifications/TonerDiluluskan.php`

```php
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

    public function __construct(public readonly PermohonanToner $permohonan) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[ICTServe] Permohonan Toner Diluluskan — {$this->permohonan->no_tiket}")
            ->greeting('Salam hormat,')
            ->line('Permohonan toner anda telah **diluluskan**.')
            ->line("**No. Tiket:** {$this->permohonan->no_tiket}")
            ->line("**Kuantiti Diluluskan:** {$this->permohonan->kuantiti_diluluskan} unit")
            ->when($this->permohonan->catatan_pentadbir, fn ($m) =>
                $m->line("**Catatan Pentadbir:** {$this->permohonan->catatan_pentadbir}")
            )
            ->line('Toner akan diserahkan kepada anda tidak lama lagi.')
            ->action('Semak Status', route('m02.butiran', $this->permohonan->no_tiket))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'no_tiket'      => $this->permohonan->no_tiket,
            'jenis'         => 'toner_diluluskan',
            'mesej'         => "Permohonan {$this->permohonan->no_tiket} telah diluluskan.",
        ];
    }
}
```
