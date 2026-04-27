# `app/Models/PenghantaranToner.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenghantaranToner extends Model
{
    protected $table = 'penghantaran_toner';

    protected $fillable = [
        'permohonan_id',
        'dihantar_oleh',
        'kuantiti_dihantar',
        'catatan',
        'tarikh_hantar',
    ];

    protected $casts = [
        'tarikh_hantar' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanToner::class, 'permohonan_id');
    }

    public function dihantar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dihantar_oleh');
    }
}
```
