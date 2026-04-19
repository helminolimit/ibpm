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
        Schema::create('log_toner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_toner_id')->constrained('permohonan_toner')->cascadeOnDelete();
            $table->string('tindakan');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('permohonan_toner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_toner');
    }
};
