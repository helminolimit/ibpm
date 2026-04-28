# 02 — Eloquent Models
## M03 Penamatan Akaun Login Komputer

Cipta 2 model baharu. Model `User` sedia ada — tambah relationships sahaja.

---

## Model 1: `PermohonanPenamatan`

**Fail:** `app/Models/PermohonanPenamatan.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermohonanPenamatan extends Model
{
    protected $table = 'permohonan_penamatan';

    protected $fillable = [
        'no_tiket',
        'pemohon_id',
        'pengguna_sasaran_id',
        'id_login_komputer',
        'tarikh_berkuat_kuasa',
        'jenis_tindakan',
        'sebab_penamatan',
        'status',
        'catatan_pentadbir',
        'tarikh_selesai',
    ];

    protected $casts = [
        'tarikh_berkuat_kuasa' => 'date',
        'tarikh_selesai'       => 'datetime',
    ];

    // --- Relationships ---

    // Pemohon yang membuat permohonan ini
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    // Pengguna yang akaun loginnya hendak ditamatkan
    public function penggunaSasaran(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_sasaran_id');
    }

    // Rekod kelulusan dua peringkat untuk permohonan ini
    public function kelulusan(): HasMany
    {
        return $this->hasMany(Kelulusan::class, 'permohonan_id');
    }

    // Log audit semua tindakan pada permohonan ini
    public function logAudit(): HasMany
    {
        return $this->hasMany(LogAudit::class, 'permohonan_id');
    }

    // Notifikasi emel yang dijana untuk permohonan ini
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'permohonan_id');
    }

    // --- Helpers ---

    // Jana nombor tiket unik format PAK-YYYY-NNN
    public static function janaNoTiket(): string
    {
        $tahun  = now()->format('Y');
        $terkini = static::whereYear('created_at', $tahun)->count() + 1;
        return 'PAK-' . $tahun . '-' . str_pad($terkini, 3, '0', STR_PAD_LEFT);
    }

    // Semak sama ada permohonan boleh diedit oleh pemohon
    public function bolehedit(): bool
    {
        return $this->status === 'DRAF';
    }
}
```

---

## Model 2: `Kelulusan`

**Fail:** `app/Models/Kelulusan.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kelulusan extends Model
{
    protected $table = 'kelulusan';

    protected $fillable = [
        'permohonan_id',
        'pelulus_id',
        'peringkat',
        'keputusan',
        'catatan',
        'tarikh_tindakan',
    ];

    protected $casts = [
        'tarikh_tindakan' => 'datetime',
    ];

    // Permohonan yang berkaitan dengan rekod kelulusan ini
    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPenamatan::class, 'permohonan_id');
    }

    // Pegawai yang membuat keputusan kelulusan ini
    public function pelulus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelulus_id');
    }
}
```

---

## Tambah ke Model `User` (sedia ada)

```php
// Dalam app/Models/User.php — tambah relationships berikut:

// Semua permohonan penamatan yang dibuat oleh pengguna ini
public function permohonanPenamatan(): HasMany
{
    return $this->hasMany(PermohonanPenamatan::class, 'pemohon_id');
}

// Semua permohonan yang menjadikan pengguna ini sebagai sasaran penamatan
public function penamatan(): HasMany
{
    return $this->hasMany(PermohonanPenamatan::class, 'pengguna_sasaran_id');
}

// Kelulusan yang telah dibuat oleh pengguna ini (sebagai pelulus)
public function kelulusan(): HasMany
{
    return $this->hasMany(Kelulusan::class, 'pelulus_id');
}
```

---

## Model 3 & 4: `LogAudit` & `Notifikasi`

`app/Models/LogAudit.php` — `$timestamps=false`, kolum: `permohonan_id, pengguna_id, tindakan, butiran(json), modul, ip_address, created_at`. Relationships: `belongsTo` PermohonanPenamatan + User.

`app/Models/Notifikasi.php` — kolum: `permohonan_id, penerima_id, jenis, tajuk, mesej, dibaca(bool), dihantar_pada`. Relationships: `belongsTo` User + PermohonanPenamatan.
