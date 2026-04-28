<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'permohonan_penamatan_id',
    'pelulus_id',
    'peringkat',
    'keputusan',
    'catatan',
    'diluluskan_pada',
])]
class KelulusanPenamatan extends Model
{
    protected $table = 'kelulusan_penamatan';

    protected function casts(): array
    {
        return [
            'diluluskan_pada' => 'datetime',
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPenamatan::class, 'permohonan_penamatan_id');
    }

    public function pelulus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelulus_id');
    }
}
