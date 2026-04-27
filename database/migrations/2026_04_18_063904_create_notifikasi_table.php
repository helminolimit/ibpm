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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aduan_ict_id')->constrained('aduan_ict')->cascadeOnDelete();
            $table->string('jenis')->comment('pengesahan, makluman');
            $table->string('penerima');
            $table->string('status')->default('hantar');
            $table->text('ralat')->nullable();
            $table->timestamps();

            $table->index(['aduan_ict_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
