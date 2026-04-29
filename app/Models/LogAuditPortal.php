<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAuditPortal extends Model
{
    protected $table = 'log_audit_portals';

    public $timestamps = false;

    protected $fillable = [
        'permohonan_portal_id',
        'pengguna_id',
        'tindakan',
        'butiran',
        'modul',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'butiran' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPortal::class, 'permohonan_portal_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
