<?php

namespace App\Events;

use App\Enums\StatusAduan;
use App\Models\AduanIct;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusDikemaskini
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AduanIct $aduan,
        public readonly StatusAduan $status,
    ) {}
}
