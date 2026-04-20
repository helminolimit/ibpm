<?php

namespace App\Enums;

enum LoanStatus: string
{
    case MenungguSokongan = 'menunggu_sokongan';
    case TidakDisokong = 'tidak_disokong';
    case DalamTindakan = 'dalam_tindakan';
    case Dipinjam = 'dipinjam';
    case LewatPulang = 'lewat_pulang';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::MenungguSokongan => 'Menunggu Sokongan',
            self::TidakDisokong => 'Tidak Disokong',
            self::DalamTindakan => 'Dalam Tindakan',
            self::Dipinjam => 'Dipinjam',
            self::LewatPulang => 'Lewat Pulang',
            self::Selesai => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MenungguSokongan => 'yellow',
            self::TidakDisokong => 'red',
            self::DalamTindakan => 'blue',
            self::Dipinjam => 'green',
            self::LewatPulang => 'orange',
            self::Selesai => 'zinc',
        };
    }
}
