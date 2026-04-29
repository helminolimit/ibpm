# UC04 — Terima Notifikasi Emel

Modul: M04 Kemaskini Portal  
Pelakon: Pemohon (terima bila status berubah), Pentadbir (terima bila permohonan baru)

---

## Keperluan

- Dua jenis emel: permohonan baru & status dikemaskini
- Guna Laravel `Mailable` + `Queue`
- Hantar dalam masa 5 minit selepas tindakan
- Simpan rekod dalam jadual `notifikasis`

---

## Migration Notifikasi

```php
Schema::create('notifikasis', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('pengguna_id')->constrained('penggunas');
    $table->foreignUuid('permohonan_id')->constrained('permohonan_portals');
    $table->enum('jenis', ['permohonan_baru', 'status_dikemaskini']);
    $table->text('mesej');
    $table->boolean('dibaca')->default(false);
    $table->timestamp('masa_hantar')->useCurrent();
    $table->timestamps();
});
```

---

## Mailable 1 — Permohonan Baru (ke Pentadbir)

```php
// app/Mail/PermohonanDiterima.php

class PermohonanDiterima extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PermohonanPortal $permohonan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ICTServe] Permohonan Kemaskini Portal Baru — ' . $this->permohonan->no_tiket,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.m04.permohonan-diterima',
        );
    }
}
```

---

## Mailable 2 — Status Dikemaskini (ke Pemohon)

```php
// app/Mail/StatusDikemaskini.php

class StatusDikemaskini extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PermohonanPortal $permohonan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[ICTServe] Status Permohonan ' . $this->permohonan->no_tiket . ' Dikemaskini',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.m04.status-dikemaskini',
        );
    }
}
```

---

## Blade Emel — Permohonan Baru

```blade
{{-- resources/views/emails/m04/permohonan-diterima.blade.php --}}
<p>Tuan/Puan,</p>
<p>Permohonan kemaskini portal baharu telah diterima.</p>
<table>
    <tr><td>No. Tiket</td><td>{{ $permohonan->no_tiket }}</td></tr>
    <tr><td>URL Halaman</td><td>{{ $permohonan->url_halaman }}</td></tr>
    <tr><td>Jenis</td><td>{{ $permohonan->jenis_perubahan }}</td></tr>
    <tr><td>Butiran</td><td>{{ $permohonan->butiran_kemaskini }}</td></tr>
    <tr><td>Pemohon</td><td>{{ $permohonan->pemohon->nama }}</td></tr>
    <tr><td>Tarikh</td><td>{{ $permohonan->tarikh_mohon->format('d M Y, H:i') }}</td></tr>
</table>
<p>Sila log masuk ke sistem ICTServe untuk mengambil tindakan.</p>
```

---

## Blade Emel — Status Dikemaskini

```blade
{{-- resources/views/emails/m04/status-dikemaskini.blade.php --}}
<p>Tuan/Puan {{ $permohonan->pemohon->nama }},</p>
<p>Status permohonan anda telah dikemaskini.</p>
<table>
    <tr><td>No. Tiket</td><td>{{ $permohonan->no_tiket }}</td></tr>
    <tr><td>Status Terkini</td><td>{{ ucfirst(str_replace('_', ' ', $permohonan->status)) }}</td></tr>
</table>
<p>Sila log masuk ke ICTServe untuk maklumat lanjut.</p>
```

---

## Hantar Emel + Simpan Notifikasi

```php
// Dalam controller / action

Mail::to($emailPentadbir)->queue(new PermohonanDiterima($permohonan));

Notifikasi::create([
    'pengguna_id'    => $idPentadbir,
    'permohonan_id'  => $permohonan->id,
    'jenis'          => 'permohonan_baru',
    'mesej'          => 'Permohonan ' . $permohonan->no_tiket . ' diterima.',
]);
```

---

## Queue Config (.env)

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work --queue=default
```

---

## Larangan

- Jangan hantar emel secara `sync` dalam production — guna `queue`
- Jangan hardcode emel pentadbir — ambil dari database mengikut `bahagian`
- Jangan skip simpan rekod `notifikasis`

---

## Kriteria Penerimaan

- [ ] Pentadbir terima emel dalam masa 5 minit selepas permohonan dihantar
- [ ] Pemohon terima emel bila status berubah
- [ ] Rekod notifikasi tersimpan dalam `notifikasis`
- [ ] Emel mengandungi no. tiket dan butiran ringkas

---

*ICTServe M04 | UC04 | April 2026*
