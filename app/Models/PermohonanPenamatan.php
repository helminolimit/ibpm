<?php

namespace App\Models;

use App\Enums\StatusPermohonanPenamatan;
use Database\Factories\PermohonanPenamatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'no_tiket',
    'pemohon_id',
    'pengguna_sasaran_id',
    'id_login_komputer',
    'tarikh_berkuat_kuasa',
    'jenis_tindakan',
    'sebab_penamatan',
    'status',
    'catatan_admin',
    'tarikh_selesai',
])]
class PermohonanPenamatan extends Model
{
    /** @use HasFactory<PermohonanPenamatanFactory> */
    use HasFactory;

    protected $table = 'permohonan_penamatan';

    protected function casts(): array
    {
        return [
            'status' => StatusPermohonanPenamatan::class,
            'tarikh_berkuat_kuasa' => 'date',
            'tarikh_selesai' => 'datetime',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function penggunaSasaran(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_sasaran_id');
    }

    public function kelulusan(): HasMany
    {
        return $this->hasMany(KelulusanPenamatan::class, 'permohonan_penamatan_id');
    }

    public function logAudit(): HasMany
    {
        return $this->hasMany(LogAudit::class, 'permohonan_penamatan_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(NotifikasiPenamatan::class, 'permohonan_penamatan_id');
    }

    public function bolehEdit(): bool
    {
        return $this->status === StatusPermohonanPenamatan::Draf;
    }

    public function scopeMilik(Builder $query, User $user): Builder
    {
        return $query->where('pemohon_id', $user->id);
    }

    public static function janaNoTiket(): string
    {
        $year = now()->year;
        $prefix = "PAK-{$year}-";

        $latest = static::where('no_tiket', 'like', "{$prefix}%")
            ->orderByDesc('no_tiket')
            ->value('no_tiket');

        $next = $latest
            ? (int) substr($latest, strlen($prefix)) + 1
            : 1;

        return $prefix.str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
