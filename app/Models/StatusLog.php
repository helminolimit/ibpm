<?php

namespace App\Models;

use App\Enums\StatusAduan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['aduan_ict_id', 'status', 'catatan', 'user_id'])]
class StatusLog extends Model
{
    protected $table = 'status_log';

    protected function casts(): array
    {
        return [
            'status' => StatusAduan::class,
        ];
    }

    public function aduanIct(): BelongsTo
    {
        return $this->belongsTo(AduanIct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
