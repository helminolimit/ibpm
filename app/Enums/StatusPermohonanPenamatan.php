<?php

namespace App\Enums;

enum StatusPermohonanPenamatan: string
{
    case Draf = 'DRAF';
    case MenungguKel1 = 'MENUNGGU_KEL_1';
    case MenungguKel2 = 'MENUNGGU_KEL_2';
    case DalamProses = 'DALAM_PROSES';
    case Selesai = 'SELESAI';
    case Ditolak = 'DITOLAK';

    public function label(): string
    {
        return match ($this) {
            StatusPermohonanPenamatan::Draf => 'Draf',
            StatusPermohonanPenamatan::MenungguKel1 => 'Menunggu Kelulusan 1',
            StatusPermohonanPenamatan::MenungguKel2 => 'Menunggu Kelulusan 2',
            StatusPermohonanPenamatan::DalamProses => 'Dalam Proses',
            StatusPermohonanPenamatan::Selesai => 'Selesai',
            StatusPermohonanPenamatan::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            StatusPermohonanPenamatan::Draf => 'zinc',
            StatusPermohonanPenamatan::MenungguKel1 => 'yellow',
            StatusPermohonanPenamatan::MenungguKel2 => 'orange',
            StatusPermohonanPenamatan::DalamProses => 'blue',
            StatusPermohonanPenamatan::Selesai => 'green',
            StatusPermohonanPenamatan::Ditolak => 'red',
        };
    }
}
