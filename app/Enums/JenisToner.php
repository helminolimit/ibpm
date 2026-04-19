<?php

namespace App\Enums;

enum JenisToner: string
{
    case Hitam = 'hitam';
    case Cyan = 'cyan';
    case Magenta = 'magenta';
    case Kuning = 'kuning';

    public function label(): string
    {
        return match ($this) {
            JenisToner::Hitam => 'Hitam',
            JenisToner::Cyan => 'Cyan',
            JenisToner::Magenta => 'Magenta',
            JenisToner::Kuning => 'Kuning',
        };
    }

    public function color(): string
    {
        return match ($this) {
            JenisToner::Hitam => 'zinc',
            JenisToner::Cyan => 'cyan',
            JenisToner::Magenta => 'pink',
            JenisToner::Kuning => 'yellow',
        };
    }
}
