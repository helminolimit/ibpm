# UC-M06-07 — Hantar Notifikasi Emel

## Actor
Sistem (auto trigger)

## Trigger Points
| Event | Penerima | Templat |
|-------|----------|---------|
| Permohonan dihantar | Pemohon + Pentadbir | `m06.permohonan_diterima` |
| Permohonan dilulus | Pemohon | `m06.permohonan_dilulus` |
| Permohonan ditolak | Pemohon | `m06.permohonan_ditolak` |
| Permohonan selesai | Pemohon | `m06.permohonan_selesai` |

## Shared Service
Guna `NotificationService` yang sama dengan M01, M02, M03, M04, M05.

```php
// App\Services\NotificationService.php (shared)
NotificationService::send(
    user: $permohonan->user,
    type: 'email',
    subject: 'Permohonan Kumpulan Emel Diterima - ' . $permohonan->no_tiket,
    template: 'm06.permohonan_diterima',
    data: ['permohonan' => $permohonan]
);
```

## Mail Templates (Blade)
```
resources/views/emails/m06/permohonan_diterima.blade.php
resources/views/emails/m06/permohonan_dilulus.blade.php
resources/views/emails/m06/permohonan_ditolak.blade.php
resources/views/emails/m06/permohonan_selesai.blade.php
```

## Notifikasi In-App
Simpan ke table `notifikasi` (shared M01-M06):
```php
Notifikasi::create([
    'permohonan_id' => $permohonan->id,
    'user_id'       => $permohonan->user_id,
    'jenis'         => 'kemaskini_status',
    'tajuk'         => 'Status permohonan dikemaskini',
    'mesej'         => 'Permohonan ' . $permohonan->no_tiket . ' telah ' . $permohonan->status,
    'dibaca'        => false,
]);
```

## DO NOT
- Buat table `notifikasi` baru — sudah ada dari M01
- Hantar emel secara synchronous — guna `Mail::queue()`
- Hard-code nama penerima — ambil dari `users` table
