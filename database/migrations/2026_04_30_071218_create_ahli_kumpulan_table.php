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
        Schema::create('ahli_kumpulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_emel')->cascadeOnDelete();
            $table->string('nama_ahli');
            $table->string('emel_ahli');
            $table->enum('tindakan', ['tambah', 'buang']);
            $table->timestamps();

            $table->unique(['permohonan_id', 'emel_ahli']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahli_kumpulan');
    }
};
