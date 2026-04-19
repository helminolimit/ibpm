<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenghantaranToner extends Model
{
    protected $table = 'penghantaran_toner';

    /** @var list<string> */
    protected $fillable = [
        'permohonan_toner_id',
        'dihantar_oleh',
        'kuantiti_dihantar',
        'catatan',
        'tarikh_hantar',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'tarikh_hantar' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanToner::class, 'permohonan_toner_id');
    }

    public function pentadbir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dihantar_oleh');
    }
}
