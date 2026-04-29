<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lampiran extends Model
{
    protected $fillable = [
        'permohonan_portal_id',
        'nama_fail',
        'path_fail',
        'jenis_fail',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPortal::class, 'permohonan_portal_id');
    }
}
