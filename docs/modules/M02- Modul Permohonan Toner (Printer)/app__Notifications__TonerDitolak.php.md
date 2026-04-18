# `app/Notifications/TonerDitolak.php`

```php
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

    public function __construct(public readonly PermohonanToner $permohonan) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[ICTServe] Permohonan Toner Ditolak — {$this->permohonan->no_tiket}")
            ->greeting('Salam hormat,')
            ->line('Maaf, permohonan toner anda telah **ditolak**.')
            ->line("**No. Tiket:** {$this->permohonan->no_tiket}")
            ->line("**Sebab Penolakan:** {$this->permohonan->catatan_pentadbir}")
            ->line('Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna untuk maklumat lanjut.')
            ->action('Hantar Permohonan Baru', route('m02.borang'))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'no_tiket'      => $this->permohonan->no_tiket,
            'jenis'         => 'toner_ditolak',
            'mesej'         => "Permohonan {$this->permohonan->no_tiket} telah ditolak.",
        ];
    }
}
```
