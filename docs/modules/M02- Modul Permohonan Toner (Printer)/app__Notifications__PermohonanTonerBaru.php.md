# `app/Notifications/PermohonanTonerBaru.php`

```php
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

    public function __construct(
        public readonly PermohonanToner $permohonan,
        public readonly string $penerima = 'pemohon' // 'pemohon' atau 'pentadbir'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->penerima === 'pentadbir') {
            return (new MailMessage)
                ->subject("[ICTServe] Permohonan Toner Baru — {$this->permohonan->no_tiket}")
                ->greeting('Salam hormat,')
                ->line('Terdapat permohonan toner baharu yang memerlukan tindakan anda.')
                ->line("**No. Tiket:** {$this->permohonan->no_tiket}")
                ->line("**Pemohon:** {$this->permohonan->pemohon->name}")
                ->line("**Model Pencetak:** {$this->permohonan->model_pencetak}")
                ->line("**Jenis Toner:** {$this->permohonan->jenis_toner}")
                ->line("**Kuantiti:** {$this->permohonan->kuantiti_diminta} unit")
                ->action('Lihat Permohonan', route('admin.m02.proses', $this->permohonan->id))
                ->salutation('Sistem ICTServe | BPM MOTAC');
        }

        return (new MailMessage)
            ->subject("[ICTServe] Permohonan Toner Anda Telah Diterima — {$this->permohonan->no_tiket}")
            ->greeting('Salam hormat,')
            ->line('Permohonan toner anda telah berjaya dihantar.')
            ->line("**No. Tiket:** {$this->permohonan->no_tiket}")
            ->line("**Model Pencetak:** {$this->permohonan->model_pencetak}")
            ->line("**Jenis Toner:** {$this->permohonan->jenis_toner}")
            ->line("**Kuantiti:** {$this->permohonan->kuantiti_diminta} unit")
            ->line('Permohonan anda akan disemak dalam masa **3 hari bekerja**.')
            ->action('Semak Status', route('m02.butiran', $this->permohonan->no_tiket))
            ->salutation('Sistem ICTServe | BPM MOTAC');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'no_tiket'      => $this->permohonan->no_tiket,
            'jenis'         => 'toner_baru',
            'mesej'         => "Permohonan toner {$this->permohonan->no_tiket} telah diterima.",
        ];
    }
}
```
