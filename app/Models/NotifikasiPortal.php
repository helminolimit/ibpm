<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiPortal extends Model
{
    protected $fillable = [
        'pengguna_id',
        'permohonan_portal_id',
        'jenis',
        'mesej',
        'dibaca',
        'masa_hantar',
    ];

    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
            'masa_hantar' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPortal::class, 'permohonan_portal_id');
    }

    public function tandaDibaca(): void
    {
        $this->update(['dibaca' => true]);
    }
}
