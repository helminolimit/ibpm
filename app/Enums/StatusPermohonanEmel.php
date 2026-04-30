<?php

namespace App\Enums;

enum StatusPermohonanEmel: string
{
    case Baru = 'baru';
    case DalamTindakan = 'dalam_tindakan';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            StatusPermohonanEmel::Baru => 'Baru',
            StatusPermohonanEmel::DalamTindakan => 'Dalam Tindakan',
            StatusPermohonanEmel::Selesai => 'Selesai',
            StatusPermohonanEmel::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            StatusPermohonanEmel::Baru => 'blue',
            StatusPermohonanEmel::DalamTindakan => 'yellow',
            StatusPermohonanEmel::Selesai => 'green',
            StatusPermohonanEmel::Ditolak => 'red',
        };
    }
}
