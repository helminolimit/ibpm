<?php

namespace App\Models;

use App\Enums\StatusPermohonanPortal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermohonanPortal extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_tiket',
        'pemohon_id',
        'pentadbir_id',
        'url_halaman',
        'jenis_perubahan',
        'butiran_kemaskini',
        'status',
        'tarikh_selesai',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPermohonanPortal::class,
            'tarikh_mohon' => 'datetime',
            'tarikh_selesai' => 'datetime',
        ];
    }

    public static function janaNoTiket(): string
    {
        $tahun = now()->year;
        $terakhir = static::whereYear('created_at', $tahun)->count() + 1;

        return sprintf('#ICT-%d-%03d', $tahun, $terakhir);
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function pentadbir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pentadbir_id');
    }

    public function lampirans(): HasMany
    {
        return $this->hasMany(Lampiran::class, 'permohonan_portal_id');
    }

    public function logAudits(): HasMany
    {
        return $this->hasMany(LogAuditPortal::class, 'permohonan_portal_id');
    }
}
