<?php

namespace App\Models;

use App\Enums\JenisToner;
use App\Enums\StatusPermohonanToner;
use Database\Factories\PermohonanTonerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'no_tiket',
    'user_id',
    'model_pencetak',
    'jenama_toner',
    'jenis_toner',
    'no_siri_toner',
    'kuantiti',
    'kuantiti_diluluskan',
    'lokasi_pencetak',
    'tujuan',
    'tarikh_diperlukan',
    'status',
])]
class PermohonanToner extends Model
{
    /** @use HasFactory<PermohonanTonerFactory> */
    use HasFactory;

    protected $table = 'permohonan_toner';

    protected function casts(): array
    {
        return [
            'jenis_toner' => JenisToner::class,
            'status' => StatusPermohonanToner::class,
            'tarikh_diperlukan' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogToner::class);
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(LampiranToner::class);
    }

    public function penghantaran(): HasOne
    {
        return $this->hasOne(PenghantaranToner::class);
    }

    public static function janaNoTiket(): string
    {
        $year = now()->year;
        $prefix = "TON-{$year}-";

        $latest = static::where('no_tiket', 'like', "{$prefix}%")
            ->orderByDesc('no_tiket')
            ->value('no_tiket');

        $next = $latest
            ? (int) substr($latest, strlen($prefix)) + 1
            : 1;

        return $prefix.str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
