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
        Schema::create('lampiran_toner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_toner_id')->constrained('permohonan_toner')->cascadeOnDelete();
            $table->string('nama_fail');
            $table->string('path');
            $table->string('jenis_fail');
            $table->unsignedBigInteger('saiz');
            $table->timestamps();

            $table->index('permohonan_toner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran_toner');
    }
};
