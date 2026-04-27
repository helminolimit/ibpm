# `app/Models/PermohonanToner.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermohonanToner extends Model
{
    protected $table = 'permohonan_toner';

    protected $fillable = [
        'no_tiket',
        'pemohon_id',
        'diproses_oleh',
        'stok_toner_id',
        'model_pencetak',
        'jenama_toner',
        'jenis_toner',
        'no_siri_toner',
        'kuantiti_diminta',
        'kuantiti_diluluskan',
        'lokasi_pencetak',
        'bahagian_pemohon',
        'tujuan',
        'catatan_pentadbir',
        'lampiran',
        'status',
        'tarikh_diperlukan',
        'submitted_at',
    ];

    protected $casts = [
        'tarikh_diperlukan' => 'date',
        'submitted_at'      => 'datetime',
    ];

    // Label status dalam Bahasa Melayu
    public function labelStatus(): string
    {
        return match ($this->status) {
            'submitted'     => 'Dihantar',
            'reviewing'     => 'Dalam Semakan',
            'approved'      => 'Diluluskan',
            'delivered'     => 'Toner Dihantar',
            'rejected'      => 'Ditolak',
            'pending_stock' => 'Menunggu Stok',
            default         => 'Tidak Diketahui',
        };
    }

    // Warna badge Tailwind mengikut status
    public function warnaStatus(): string
    {
        return match ($this->status) {
            'submitted'     => 'bg-blue-100 text-blue-800',
            'reviewing'     => 'bg-orange-100 text-orange-800',
            'approved'      => 'bg-green-100 text-green-800',
            'delivered'     => 'bg-emerald-100 text-emerald-800',
            'rejected'      => 'bg-red-100 text-red-800',
            'pending_stock' => 'bg-yellow-100 text-yellow-800',
            default         => 'bg-gray-100 text-gray-800',
        };
    }

    // Jana no. tiket format #TON-YYYY-NNN
    public static function janaNoTiket(): string
    {
        $tahun  = now()->year;
        $prefix = "TON-{$tahun}-";

        $terkini = static::where('no_tiket', 'like', "#TON-{$tahun}-%")
            ->orderByDesc('id')
            ->first();

        $urutan = $terkini
            ? (int) substr($terkini->no_tiket, -3) + 1
            : 1;

        return '#' . $prefix . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function diprosesoleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function stokToner(): BelongsTo
    {
        return $this->belongsTo(StokToner::class, 'stok_toner_id');
    }

    public function penghantaran(): HasMany
    {
        return $this->hasMany(PenghantaranToner::class, 'permohonan_id');
    }

    public function log(): HasMany
    {
        return $this->hasMany(LogToner::class, 'permohonan_id');
    }
}
```
