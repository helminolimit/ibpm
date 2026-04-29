<?php

namespace App\Enums;

enum StatusPermohonanPortal: string
{
    case Diterima = 'diterima';
    case DalamProses = 'dalam_proses';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Diterima => 'Diterima',
            self::DalamProses => 'Dalam Proses',
            self::Selesai => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Diterima => 'blue',
            self::DalamProses => 'yellow',
            self::Selesai => 'green',
        };
    }
}
