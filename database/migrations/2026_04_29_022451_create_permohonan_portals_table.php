<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_portals', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('pemohon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pentadbir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('url_halaman');
            $table->string('jenis_perubahan');
            $table->text('butiran_kemaskini');
            $table->string('status')->default('diterima');
            $table->timestamp('tarikh_mohon')->useCurrent();
            $table->timestamp('tarikh_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_portals');
    }
};
