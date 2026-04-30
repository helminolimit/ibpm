<?php

namespace App\Models;

use App\Enums\StatusPermohonanPortal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Scope a query to only include applications belonging to the authenticated user.
     *
     * This scope enforces security at the database query level by filtering records
     * where pemohon_id matches the authenticated user's ID.
     */
    public function scopeMilikPemohon(Builder $query): Builder
    {
        return $query->where('pemohon_id', Auth::id());
    }

    /**
     * Scope a query to filter applications by ticket number or page URL.
     *
     * This scope enables search functionality by filtering records where either
     * the ticket number OR the page URL contains the search query.
     */
    public function scopeCarian(Builder $query, string $carian): Builder
    {
        return $query->where(function ($q) use ($carian) {
            $q->where('no_tiket', 'like', "%{$carian}%")
                ->orWhere('url_halaman', 'like', "%{$carian}%");
        });
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

    public function tugasans(): HasMany
    {
        return $this->hasMany(TugasanPortal::class, 'permohonan_portal_id');
    }
}
