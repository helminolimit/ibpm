<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'permohonan_penamatan_id',
    'penerima_id',
    'jenis',
    'tajuk',
    'mesej',
    'dibaca',
    'dihantar_pada',
])]
class NotifikasiPenamatan extends Model
{
    protected $table = 'notifikasi_penamatan';

    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
            'dihantar_pada' => 'datetime',
        ];
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPenamatan::class, 'permohonan_penamatan_id');
    }
}
