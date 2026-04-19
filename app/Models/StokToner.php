<?php

namespace App\Models;

use App\Enums\JenisToner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokToner extends Model
{
    use HasFactory;

    protected $table = 'stok_toner';

    /** @var list<string> */
    protected $fillable = [
        'model_toner',
        'jenama',
        'jenis_toner',
        'warna',
        'kuantiti_ada',
        'kuantiti_minimum',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'jenis_toner' => JenisToner::class,
    ];

    public function stokHabis(): bool
    {
        return $this->kuantiti_ada === 0;
    }

    public function stokRendah(): bool
    {
        return $this->kuantiti_ada > 0 && $this->kuantiti_ada <= $this->kuantiti_minimum;
    }

    public function tambahStok(int $kuantiti): void
    {
        $this->increment('kuantiti_ada', $kuantiti);
    }

    public function kurangkanStok(int $kuantiti): void
    {
        $this->decrement('kuantiti_ada', $kuantiti);
    }
}
