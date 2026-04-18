# `app/Models/StokToner.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokToner extends Model
{
    protected $table = 'stok_toner';

    protected $fillable = [
        'model_toner',
        'jenama',
        'jenis',
        'warna',
        'kuantiti_ada',
        'kuantiti_minimum',
    ];

    // Semak sama ada stok mencukupi
    public function stokMencukupi(int $kuantiti): bool
    {
        return $this->kuantiti_ada >= $kuantiti;
    }

    // Semak sama ada stok rendah
    public function stokRendah(): bool
    {
        return $this->kuantiti_ada <= $this->kuantiti_minimum;
    }

    // Kurangkan stok selepas penghantaran
    public function kurangkanStok(int $kuantiti): void
    {
        $this->decrement('kuantiti_ada', $kuantiti);
    }

    // Tambah stok apabila bekalan baru diterima
    public function tambahStok(int $kuantiti): void
    {
        $this->increment('kuantiti_ada', $kuantiti);
    }

    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanToner::class, 'stok_toner_id');
    }
}
```
