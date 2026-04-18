<?php

namespace App\Models;

use App\Enums\StatusNotifikasi;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['aduan_ict_id', 'jenis', 'penerima', 'status', 'ralat'])]
class Notifikasi extends Model
{
    protected function casts(): array
    {
        return [
            'status' => StatusNotifikasi::class,
        ];
    }

    public function aduanIct(): BelongsTo
    {
        return $this->belongsTo(AduanIct::class);
    }
}
