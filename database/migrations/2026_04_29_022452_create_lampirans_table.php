<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_portal_id')->constrained('permohonan_portals')->cascadeOnDelete();
            $table->string('nama_fail');
            $table->string('path_fail');
            $table->string('jenis_fail');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampirans');
    }
};
