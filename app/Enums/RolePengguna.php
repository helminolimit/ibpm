<?php

namespace App\Enums;

enum RolePengguna: string
{
    case Pengguna = 'pengguna';
    case Pentadbir = 'pentadbir';
    case Superadmin = 'superadmin';
    case Teknician = 'teknician';

    public function label(): string
    {
        return match ($this) {
            RolePengguna::Pengguna => 'Pengguna',
            RolePengguna::Pentadbir => 'Pentadbir BPM',
            RolePengguna::Superadmin => 'Superadmin',
            RolePengguna::Teknician => 'Teknician ICT',
        };
    }
}
