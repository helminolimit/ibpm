<?php

namespace App\Enums;

enum JenisTindakan: string
{
    case Tambah = 'tambah';
    case Buang = 'buang';

    public function label(): string
    {
        return match ($this) {
            JenisTindakan::Tambah => 'Tambah Ahli',
            JenisTindakan::Buang => 'Buang Ahli',
        };
    }
}
