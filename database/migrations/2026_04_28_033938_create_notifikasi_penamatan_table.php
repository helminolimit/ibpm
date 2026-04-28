<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_penamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_penamatan_id')->constrained('permohonan_penamatan')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['HANTAR', 'KELULUSAN', 'TOLAK', 'SELESAI']);
            $table->string('tajuk');
            $table->text('mesej');
            $table->boolean('dibaca')->default(false);
            $table->timestamp('dihantar_pada')->nullable();
            $table->timestamps();

            $table->index(['penerima_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_penamatan');
    }
};
