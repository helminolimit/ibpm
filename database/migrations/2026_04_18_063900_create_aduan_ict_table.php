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
        Schema::create('aduan_ict', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kategori_aduan_id')->constrained('kategori_aduan');
            $table->string('lokasi');
            $table->string('tajuk');
            $table->text('keterangan');
            $table->string('no_telefon');
            $table->string('status')->default('baru');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduan_ict');
    }
};
