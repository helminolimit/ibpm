<?php

namespace App\Enums;

enum StatusAduan: string
{
    case Baru = 'baru';
    case DalamProses = 'dalam_proses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            StatusAduan::Baru => 'Baru',
            StatusAduan::DalamProses => 'Dalam Proses',
            StatusAduan::Selesai => 'Selesai',
            StatusAduan::Ditolak => 'Ditolak',
            StatusAduan::Dibatalkan => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            StatusAduan::Baru => 'blue',
            StatusAduan::DalamProses => 'yellow',
            StatusAduan::Selesai => 'green',
            StatusAduan::Ditolak => 'red',
            StatusAduan::Dibatalkan => 'zinc',
        };
    }
}
