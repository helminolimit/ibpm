<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokToner extends Model
{
    protected $table = 'stok_toner';

    /** @var list<string> */
    protected $fillable = ['jenis_toner', 'kuantiti_ada'];

    public function kurangkanStok(int $kuantiti): void
    {
        $this->decrement('kuantiti_ada', $kuantiti);
    }
}
