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
        Schema::create('permohonan_emel', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('pentadbir_id')->nullable()->constrained('users');
            $table->foreignId('kumpulan_emel_id')->constrained('kumpulan_emel');
            $table->enum('jenis_tindakan', ['tambah', 'buang']);
            $table->enum('status', ['baru', 'dalam_tindakan', 'selesai', 'ditolak'])->default('baru');
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_pentadbir')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_emel');
    }
};
