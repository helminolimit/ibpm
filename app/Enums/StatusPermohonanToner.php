<?php

namespace App\Enums;

enum StatusPermohonanToner: string
{
    case Submitted = 'submitted';
    case Disemak = 'disemak';
    case Diluluskan = 'diluluskan';
    case Ditolak = 'ditolak';
    case Dihantar = 'dihantar';
    case PendingStock = 'pending_stock';

    public function label(): string
    {
        return match ($this) {
            StatusPermohonanToner::Submitted => 'Dihantar',
            StatusPermohonanToner::Disemak => 'Dalam Semakan',
            StatusPermohonanToner::Diluluskan => 'Diluluskan',
            StatusPermohonanToner::Ditolak => 'Ditolak',
            StatusPermohonanToner::Dihantar => 'Toner Dihantar',
            StatusPermohonanToner::PendingStock => 'Menunggu Stok',
        };
    }

    public function color(): string
    {
        return match ($this) {
            StatusPermohonanToner::Submitted => 'blue',
            StatusPermohonanToner::Disemak => 'yellow',
            StatusPermohonanToner::Diluluskan => 'green',
            StatusPermohonanToner::Ditolak => 'red',
            StatusPermohonanToner::Dihantar => 'teal',
            StatusPermohonanToner::PendingStock => 'orange',
        };
    }
}
