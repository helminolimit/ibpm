<?php

namespace App\Enums;

enum StatusPengguna: string
{
    case Aktif = 'aktif';
    case TidakAktif = 'tidak_aktif';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            StatusPengguna::Aktif => 'Aktif',
            StatusPengguna::TidakAktif => 'Tidak Aktif',
            StatusPengguna::Pending => 'Pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            StatusPengguna::Aktif => 'green',
            StatusPengguna::TidakAktif => 'red',
            StatusPengguna::Pending => 'yellow',
        };
    }
}
