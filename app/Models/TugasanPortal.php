<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasanPortal extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_portal_id',
        'teknisian_id',
        'ditugaskan_oleh',
        'nota_tugasan',
        'status_tugasan',
        'tarikh_tugasan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_tugasan' => 'datetime',
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPortal::class, 'permohonan_portal_id');
    }

    public function teknisian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisian_id');
    }

    public function ditugaskanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditugaskan_oleh');
    }
}
