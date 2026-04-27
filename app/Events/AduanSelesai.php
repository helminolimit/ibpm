<?php

namespace App\Events;

use App\Models\AduanIct;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AduanSelesai
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly AduanIct $aduan) {}
}
