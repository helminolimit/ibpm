<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['aduan_ict_id', 'nama_fail', 'path', 'jenis_fail', 'saiz'])]
class LampiranAduan extends Model
{
    protected $table = 'lampiran_aduan';

    public function aduanIct(): BelongsTo
    {
        return $this->belongsTo(AduanIct::class);
    }
}
