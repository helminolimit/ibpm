<?php

namespace App\Models;

use App\Enums\StatusAduan;
use Database\Factories\AduanIctFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['no_tiket', 'user_id', 'kategori_aduan_id', 'lokasi', 'tajuk', 'keterangan', 'no_telefon', 'status'])]
class AduanIct extends Model
{
    /** @use HasFactory<AduanIctFactory> */
    use HasFactory;

    protected $table = 'aduan_ict';

    protected function casts(): array
    {
        return [
            'status' => StatusAduan::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriAduan::class, 'kategori_aduan_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class);
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(LampiranAduan::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Generate a unique ticket number in format ICT-YYYY-XXX.
     */
    public static function generateNoTiket(): string
    {
        $year = now()->year;
        $prefix = "ICT-{$year}-";

        $latest = static::where('no_tiket', 'like', "{$prefix}%")
            ->orderByDesc('no_tiket')
            ->value('no_tiket');

        $next = $latest
            ? (int) substr($latest, strlen($prefix)) + 1
            : 1;

        return $prefix.str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
