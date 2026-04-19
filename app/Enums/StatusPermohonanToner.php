<?php

namespace App\Enums;

enum StatusPermohonanToner: string
{
    case Submitted = 'submitted';
    case Disemak = 'disemak';
    case Diluluskan = 'diluluskan';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            StatusPermohonanToner::Submitted => 'Dihantar',
            StatusPermohonanToner::Disemak => 'Dalam Semakan',
            StatusPermohonanToner::Diluluskan => 'Diluluskan',
            StatusPermohonanToner::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            StatusPermohonanToner::Submitted => 'blue',
            StatusPermohonanToner::Disemak => 'yellow',
            StatusPermohonanToner::Diluluskan => 'green',
            StatusPermohonanToner::Ditolak => 'red',
        };
    }
}
