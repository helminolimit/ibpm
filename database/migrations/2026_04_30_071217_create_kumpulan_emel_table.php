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
        Schema::create('kumpulan_emel', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kumpulan');
            $table->string('alamat_emel')->unique();
            $table->string('pemilik_unit')->nullable();
            $table->integer('jumlah_ahli')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kumpulan_emel');
    }
};
