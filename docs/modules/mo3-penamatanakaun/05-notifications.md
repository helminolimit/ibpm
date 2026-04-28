# 05 — Notifikasi Emel & Queue
## M03 Penamatan Akaun Login Komputer

Cipta 1 kelas Notification yang mengendalikan semua jenis emel M03 melalui queue.

---

## Persediaan Queue

Pastikan `.env` dikonfigurasi untuk queue:

```env
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=ictserve@motac.gov.my
MAIL_FROM_NAME="ICTServe MOTAC"
```

Jalankan migration queue (jika belum ada):

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work --queue=default
```

---

## Notification Class
**Fail:** `app/Notifications/PenatamatanNotification.php`

```php
<?php
namespace App\Notifications;

use App\Models\PermohonanPenamatan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Kelas notifikasi tunggal untuk semua jenis emel M03
// Jenis: HANTAR | KELULUSAN | TOLAK | SELESAI
class PenatamatanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // Hantar semua emel M03 melalui queue — JANGAN hantar secara synchronous
    public function __construct(
        public readonly PermohonanPenamatan $permohonan,
        public readonly string $jenis // HANTAR | KELULUSAN | TOLAK | SELESAI
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Hantar emel DAN simpan dalam sistem notifikasi
    }

    // Bina kandungan emel berdasarkan jenis notifikasi
    public function toMail(object $notifiable): MailMessage
    {
        return match($this->jenis) {
            'HANTAR'    => $this->emailHantar($notifiable),
            'KELULUSAN' => $this->emailKelulusan($notifiable),
            'TOLAK'     => $this->emailTolak($notifiable),
            'SELESAI'   => $this->emailSelesai($notifiable),
        };
    }

    // Emel 1: Pengesahan permohonan diterima — dihantar kepada pemohon
    private function emailHantar(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ICTServe] Permohonan Penamatan Akaun Diterima — ' . $this->permohonan->no_tiket)
            ->greeting('Salam ' . $notifiable->name . ',')
            ->line('Permohonan penamatan akaun login anda telah diterima dan sedang menunggu kelulusan.')
            ->line('**No. Tiket:** ' . $this->permohonan->no_tiket)
            ->line('**ID Login:** ' . $this->permohonan->id_login_komputer)
            ->line('**Tarikh Berkuat Kuasa:** ' . $this->permohonan->tarikh_berkuat_kuasa->format('d/m/Y'))
            ->action('Semak Status', route('penamatan-akaun.show', $this->permohonan->id))
            ->line('Anda akan dimaklumkan apabila status permohonan berubah.');
    }

    // Emel 2: Makluman kelulusan peringkat 1 — kepada pemohon dan Pentadbir ICT
    private function emailKelulusan(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ICTServe] Permohonan ' . $this->permohonan->no_tiket . ' — Dalam Semakan ICT')
            ->greeting('Salam ' . $notifiable->name . ',')
            ->line('Permohonan penamatan akaun telah diluluskan pada peringkat pertama dan kini dalam semakan Pentadbir ICT.')
            ->line('**No. Tiket:** ' . $this->permohonan->no_tiket)
            ->line('**ID Login:** ' . $this->permohonan->id_login_komputer)
            ->action('Lihat Permohonan', route('penamatan-akaun.show', $this->permohonan->id));
    }

    // Emel 3: Makluman penolakan — kepada pemohon sahaja
    private function emailTolak(object $notifiable): MailMessage
    {
        // Ambil catatan penolakan dari rekod kelulusan terkini
        $catatan = $this->permohonan->kelulusan()->latest()->first()?->catatan ?? 'Tiada catatan diberikan.';

        return (new MailMessage)
            ->subject('[ICTServe] Permohonan ' . $this->permohonan->no_tiket . ' — Ditolak')
            ->greeting('Salam ' . $notifiable->name . ',')
            ->line('Permohonan penamatan akaun anda **telah ditolak**.')
            ->line('**No. Tiket:** ' . $this->permohonan->no_tiket)
            ->line('**Sebab Penolakan:** ' . $catatan)
            ->line('Sila hubungi Bahagian Pengurusan Maklumat untuk maklumat lanjut atau hantar permohonan baru.')
            ->action('Hantar Permohonan Baru', route('penamatan-akaun.create'));
    }

    // Emel 4: Pengesahan akaun telah ditamatkan — kepada pemohon
    private function emailSelesai(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ICTServe] Akaun ' . $this->permohonan->id_login_komputer . ' — Berjaya Ditamatkan')
            ->greeting('Salam ' . $notifiable->name . ',')
            ->line('Akaun login komputer berikut telah berjaya ditamatkan.')
            ->line('**No. Tiket:** ' . $this->permohonan->no_tiket)
            ->line('**ID Login:** ' . $this->permohonan->id_login_komputer)
            ->line('**Tarikh Selesai:** ' . $this->permohonan->tarikh_selesai?->format('d/m/Y H:i'))
            ->line('Rekod ini telah disimpan dalam sistem ICTServe untuk rujukan audit.');
    }

    // Simpan notifikasi dalam jadual `notifikasi` untuk paparan dalam sistem
    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'no_tiket'      => $this->permohonan->no_tiket,
            'jenis'         => $this->jenis,
            'id_login'      => $this->permohonan->id_login_komputer,
        ];
    }
}
```

---

## Cara Panggil Notifikasi dalam Controller

```php
use App\Notifications\PenatamatanNotification;

// Hantar kepada pemohon
$permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'HANTAR'));

// Hantar kepada pentadbir ICT (semua pengguna berperanan pentadbir)
$pentadbir = User::where('peranan', 'pentadbir')->get();
Notification::send($pentadbir, new PenatamatanNotification($permohonan, 'KELULUSAN'));
```

---

## Rekod Notifikasi ke Jadual `notifikasi`

Selepas notify, simpan juga ke jadual `notifikasi` (untuk paparan loceng dalam sistem):

```php
$permohonan->notifikasi()->create([
    'penerima_id'  => $permohonan->pemohon_id,
    'jenis'        => 'HANTAR',
    'tajuk'        => 'Permohonan ' . $permohonan->no_tiket . ' diterima',
    'mesej'        => 'Permohonan penamatan akaun ' . $permohonan->id_login_komputer . ' sedang diproses.',
    'dihantar_pada' => now(),
]);
```

---

## JANGAN

- Jangan hantar emel terus tanpa queue — akan melambatkan response
- Jangan panggil `sendNow()` — guna `notify()` sahaja supaya ShouldQueue berfungsi
- Jangan hardcode alamat emel penerima — ambil dari model `User`
