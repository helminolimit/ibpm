<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelulusan_penamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_penamatan_id')->constrained('permohonan_penamatan')->cascadeOnDelete();
            $table->foreignId('pelulus_id')->constrained('users')->cascadeOnDelete();
            $table->enum('peringkat', ['PERINGKAT_1', 'PERINGKAT_2']);
            $table->enum('keputusan', ['LULUS', 'TOLAK']);
            $table->text('catatan')->nullable();
            $table->timestamp('diluluskan_pada');
            $table->timestamps();

            $table->index(['permohonan_penamatan_id', 'peringkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelulusan_penamatan');
    }
};
