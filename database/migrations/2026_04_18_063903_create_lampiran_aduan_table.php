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
        Schema::create('lampiran_aduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aduan_ict_id')->constrained('aduan_ict')->cascadeOnDelete();
            $table->string('nama_fail');
            $table->string('path');
            $table->string('jenis_fail');
            $table->unsignedBigInteger('saiz')->comment('in bytes');
            $table->timestamps();

            $table->index('aduan_ict_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran_aduan');
    }
};
