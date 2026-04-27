# `2026_04_02_000001_create_stok_toner_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_toner', function (Blueprint $table) {
            $table->id();
            $table->string('model_toner', 100);
            $table->string('jenama', 100);
            $table->string('jenis', 50);   // hitam, cyan, magenta, kuning
            $table->string('warna', 50)->nullable();
            $table->unsignedInteger('kuantiti_ada')->default(0);
            $table->unsignedInteger('kuantiti_minimum')->default(5);
            $table->timestamps();

            $table->unique(['model_toner', 'jenama', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_toner');
    }
};
```
