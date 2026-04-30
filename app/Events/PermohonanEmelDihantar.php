<?php

namespace App\Events;

use App\Models\PermohonanEmel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermohonanEmelDihantar
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly PermohonanEmel $permohonan) {}
}
