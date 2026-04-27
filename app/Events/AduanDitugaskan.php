<?php

namespace App\Events;

use App\Models\AduanIct;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AduanDitugaskan
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AduanIct $aduan,
        public readonly User $teknician,
        public readonly ?string $catatanArahan = null,
    ) {}
}
