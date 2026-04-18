# `2026_04_02_000004_create_log_toner_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_toner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_toner')->cascadeOnDelete();
            $table->foreignId('pengguna_id')->constrained('users')->restrictOnDelete();
            $table->enum('tindakan', [
                'submitted',
                'reviewed',
                'approved',
                'rejected',
                'delivered',
                'stock_updated',
            ]);
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_toner');
    }
};
```
