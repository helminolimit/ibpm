# `2026_04_02_000002_create_permohonan_toner_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_toner', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket', 20)->unique();
            $table->foreignId('pemohon_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('stok_toner_id')->nullable()->constrained('stok_toner')->nullOnDelete();
            $table->string('model_pencetak', 100);
            $table->string('jenama_toner', 100);
            $table->string('jenis_toner', 50);
            $table->string('no_siri_toner', 100)->nullable();
            $table->unsignedInteger('kuantiti_diminta')->default(1);
            $table->unsignedInteger('kuantiti_diluluskan')->nullable();
            $table->string('lokasi_pencetak', 150);
            $table->string('bahagian_pemohon', 100);
            $table->text('tujuan');
            $table->text('catatan_pentadbir')->nullable();
            $table->string('lampiran', 255)->nullable();
            $table->enum('status', [
                'submitted',
                'reviewing',
                'approved',
                'delivered',
                'rejected',
                'pending_stock',
            ])->default('submitted');
            $table->date('tarikh_diperlukan')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->index('status');
            $table->index('pemohon_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_toner');
    }
};
```
