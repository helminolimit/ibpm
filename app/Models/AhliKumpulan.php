<?php

namespace App\Models;

use App\Enums\JenisTindakan;
use Database\Factories\AhliKumpulanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['permohonan_id', 'nama_ahli', 'emel_ahli', 'tindakan'])]
class AhliKumpulan extends Model
{
    /** @use HasFactory<AhliKumpulanFactory> */
    use HasFactory;

    protected $table = 'ahli_kumpulan';

    protected function casts(): array
    {
        return [
            'tindakan' => JenisTindakan::class,
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanEmel::class, 'permohonan_id');
    }
}
