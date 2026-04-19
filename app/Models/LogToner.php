<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['permohonan_toner_id', 'tindakan', 'catatan', 'user_id'])]
class LogToner extends Model
{
    protected $table = 'log_toner';

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanToner::class, 'permohonan_toner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
