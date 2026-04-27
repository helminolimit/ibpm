# `app/Models/LogToner.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogToner extends Model
{
    public $timestamps = false;

    protected $table = 'log_toner';

    protected $fillable = [
        'permohonan_id',
        'pengguna_id',
        'tindakan',
        'keterangan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Label tindakan dalam Bahasa Melayu
    public function labelTindakan(): string
    {
        return match ($this->tindakan) {
            'submitted'     => 'Permohonan dihantar',
            'reviewed'      => 'Sedang disemak',
            'approved'      => 'Diluluskan',
            'rejected'      => 'Ditolak',
            'delivered'     => 'Toner dihantar',
            'stock_updated' => 'Stok dikemaskini',
            default         => $this->tindakan,
        };
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanToner::class, 'permohonan_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
```
