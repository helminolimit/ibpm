<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['permohonan_toner_id', 'nama_fail', 'path', 'jenis_fail', 'saiz'])]
class LampiranToner extends Model
{
    protected $table = 'lampiran_toner';

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanToner::class, 'permohonan_toner_id');
    }
}
