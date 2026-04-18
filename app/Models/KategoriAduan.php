<?php

namespace App\Models;

use Database\Factories\KategoriAduanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'unit_bpm', 'emel_unit', 'is_aktif'])]
class KategoriAduan extends Model
{
    /** @use HasFactory<KategoriAduanFactory> */
    use HasFactory;

    protected $table = 'kategori_aduan';

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function aduanIct(): HasMany
    {
        return $this->hasMany(AduanIct::class, 'kategori_aduan_id');
    }

    public function scopeAktif($query): void
    {
        $query->where('is_aktif', true);
    }
}
