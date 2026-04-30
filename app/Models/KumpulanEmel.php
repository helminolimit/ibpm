<?php

namespace App\Models;

use Database\Factories\KumpulanEmelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kumpulan', 'alamat_emel', 'pemilik_unit', 'jumlah_ahli'])]
class KumpulanEmel extends Model
{
    /** @use HasFactory<KumpulanEmelFactory> */
    use HasFactory;

    protected $table = 'kumpulan_emel';

    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanEmel::class);
    }
}
