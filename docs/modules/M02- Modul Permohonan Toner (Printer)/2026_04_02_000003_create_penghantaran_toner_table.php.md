# `2026_04_02_000003_create_penghantaran_toner_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghantaran_toner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_toner')->cascadeOnDelete();
            $table->foreignId('dihantar_oleh')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('kuantiti_dihantar');
            $table->text('catatan')->nullable();
            $table->timestamp('tarikh_hantar')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghantaran_toner');
    }
};
```
