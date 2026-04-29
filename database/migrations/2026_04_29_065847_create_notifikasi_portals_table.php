<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_portals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('permohonan_portal_id')->constrained('permohonan_portals')->cascadeOnDelete();
            $table->enum('jenis', ['permohonan_baru', 'status_dikemaskini']);
            $table->text('mesej');
            $table->boolean('dibaca')->default(false);
            $table->timestamp('masa_hantar')->useCurrent();
            $table->timestamps();

            $table->index(['pengguna_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_portals');
    }
};
