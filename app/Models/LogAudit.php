<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'permohonan_penamatan_id',
    'pengguna_id',
    'tindakan',
    'butiran',
    'modul',
    'ip_address',
])]
class LogAudit extends Model
{
    protected $table = 'log_audits';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'butiran' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPenamatan::class, 'permohonan_penamatan_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
