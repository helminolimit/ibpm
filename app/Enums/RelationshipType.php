<?php

namespace App\Enums;

enum RelationshipType: string
{
    case KetuaKakitangan = 'ketua_kakitangan';
    case RakanSeunit = 'rakan_seunit';
    case TugasanRasmi = 'tugasan_rasmi';
    case LainLain = 'lain_lain';

    public function label(): string
    {
        return match ($this) {
            self::KetuaKakitangan => 'Ketua kepada Kakitangan',
            self::RakanSeunit => 'Rakan Seunit',
            self::TugasanRasmi => 'Tugasan Rasmi',
            self::LainLain => 'Lain-lain',
        };
    }
}
