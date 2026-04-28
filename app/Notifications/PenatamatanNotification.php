<?php

namespace App\Notifications;

use App\Models\PermohonanPenamatan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PenatamatanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly PermohonanPenamatan $permohonan,
        public readonly string $jenis
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return match ($this->jenis) {
            'HANTAR' => $this->emailHantar($notifiable),
            'KELULUSAN' => $this->emailKelulusan($notifiable),
            'TOLAK' => $this->emailTolak($notifiable),
            'SELESAI' => $this->emailSelesai($notifiable),
        };
    }

    private function emailHantar(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ICTServe] Permohonan Penamatan Akaun Diterima — '.$this->permohonan->no_tiket)
            ->greeting('Salam '.$notifiable->name.',')
            ->line('Permohonan penamatan akaun login anda telah diterima dan sedang menunggu kelulusan.')
            ->line('**No. Tiket:** '.$this->permohonan->no_tiket)
            ->line('**ID Login:** '.$this->permohonan->id_login_komputer)
            ->line('**Tarikh Berkuat Kuasa:** '.$this->permohonan->tarikh_berkuat_kuasa->format('d/m/Y'))
            ->action('Semak Status', route('penamatan-akaun.show', $this->permohonan->id))
            ->line('Anda akan dimaklumkan apabila status permohonan berubah.');
    }

    private function emailKelulusan(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ICTServe] Permohonan '.$this->permohonan->no_tiket.' — Dalam Semakan ICT')
            ->greeting('Salam '.$notifiable->name.',')
            ->line('Permohonan penamatan akaun telah diluluskan pada peringkat pertama dan kini dalam semakan Pentadbir ICT.')
            ->line('**No. Tiket:** '.$this->permohonan->no_tiket)
            ->line('**ID Login:** '.$this->permohonan->id_login_komputer)
            ->action('Lihat Permohonan', route('penamatan-akaun.show', $this->permohonan->id));
    }

    private function emailTolak(object $notifiable): MailMessage
    {
        $catatan = $this->permohonan->kelulusan()->latest()->first()?->catatan ?? 'Tiada catatan diberikan.';

        return (new MailMessage)
            ->subject('[ICTServe] Permohonan '.$this->permohonan->no_tiket.' — Ditolak')
            ->greeting('Salam '.$notifiable->name.',')
            ->line('Permohonan penamatan akaun anda **telah ditolak**.')
            ->line('**No. Tiket:** '.$this->permohonan->no_tiket)
            ->line('**Sebab Penolakan:** '.$catatan)
            ->line('Sila hubungi Bahagian Pengurusan Maklumat untuk maklumat lanjut atau hantar permohonan baru.')
            ->action('Hantar Permohonan Baru', route('penamatan-akaun.create'));
    }

    private function emailSelesai(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ICTServe] Akaun '.$this->permohonan->id_login_komputer.' — Berjaya Ditamatkan')
            ->greeting('Salam '.$notifiable->name.',')
            ->line('Akaun login komputer berikut telah berjaya ditamatkan.')
            ->line('**No. Tiket:** '.$this->permohonan->no_tiket)
            ->line('**ID Login:** '.$this->permohonan->id_login_komputer)
            ->line('**Tarikh Selesai:** '.$this->permohonan->tarikh_selesai?->format('d/m/Y H:i'))
            ->line('Rekod ini telah disimpan dalam sistem ICTServe untuk rujukan audit.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'no_tiket' => $this->permohonan->no_tiket,
            'jenis' => $this->jenis,
            'id_login' => $this->permohonan->id_login_komputer,
        ];
    }
}
