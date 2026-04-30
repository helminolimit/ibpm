<?php

namespace App\Models;

use App\Enums\JenisTindakan;
use App\Enums\StatusPermohonanEmel;
use Database\Factories\PermohonanEmelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['no_tiket', 'user_id', 'pentadbir_id', 'kumpulan_emel_id', 'jenis_tindakan', 'status', 'catatan_pemohon', 'catatan_pentadbir', 'selesai_at'])]
class PermohonanEmel extends Model
{
    /** @use HasFactory<PermohonanEmelFactory> */
    use HasFactory;

    protected $table = 'permohonan_emel';

    protected function casts(): array
    {
        return [
            'status' => StatusPermohonanEmel::class,
            'jenis_tindakan' => JenisTindakan::class,
            'selesai_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pentadbir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pentadbir_id');
    }

    public function kumpulanEmel(): BelongsTo
    {
        return $this->belongsTo(KumpulanEmel::class);
    }

    public function ahliKumpulan(): HasMany
    {
        return $this->hasMany(AhliKumpulan::class, 'permohonan_id');
    }

    public static function generateNoTiket(): string
    {
        $year = now()->year;
        $prefix = "GRP-{$year}-";

        $latest = static::where('no_tiket', 'like', "{$prefix}%")
            ->orderByDesc('no_tiket')
            ->value('no_tiket');

        $next = $latest
            ? (int) substr($latest, strlen($prefix)) + 1
            : 1;

        return $prefix.str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
