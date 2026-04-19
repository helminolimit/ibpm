<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penghantaran_toner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_toner_id')->constrained('permohonan_toner')->cascadeOnDelete();
            $table->foreignId('dihantar_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('kuantiti_dihantar');
            $table->text('catatan')->nullable();
            $table->timestamp('tarikh_hantar');
            $table->timestamps();

            $table->index('permohonan_toner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghantaran_toner');
    }
};
