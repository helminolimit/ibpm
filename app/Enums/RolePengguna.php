<?php

namespace App\Enums;

enum RolePengguna: string
{
    case Pengguna = 'pengguna';
    case Pentadbir = 'pentadbir';
    case Superadmin = 'superadmin';

    public function label(): string
    {
        return match ($this) {
            RolePengguna::Pengguna => 'Pengguna',
            RolePengguna::Pentadbir => 'Pentadbir BPM',
            RolePengguna::Superadmin => 'Superadmin',
        };
    }
}
