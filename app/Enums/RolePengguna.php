<?php

namespace App\Enums;

enum RolePengguna: string
{
    case Pengguna = 'pengguna';
    case Pelulus1 = 'pelulus_1';
    case Pentadbir = 'pentadbir';
    case Superadmin = 'superadmin';
    case Teknician = 'teknician';

    public function label(): string
    {
        return match ($this) {
            RolePengguna::Pengguna => 'Pengguna',
            RolePengguna::Pelulus1 => 'Pegawai Gred 41+',
            RolePengguna::Pentadbir => 'Pentadbir BPM',
            RolePengguna::Superadmin => 'Superadmin',
            RolePengguna::Teknician => 'Teknician ICT',
        };
    }
}
