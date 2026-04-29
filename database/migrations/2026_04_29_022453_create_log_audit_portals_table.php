<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_audit_portals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_portal_id')->constrained('permohonan_portals')->cascadeOnDelete();
            $table->foreignId('pengguna_id')->constrained('users')->cascadeOnDelete();
            $table->string('tindakan');
            $table->json('butiran')->nullable();
            $table->string('modul')->default('M04');
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['permohonan_portal_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_audit_portals');
    }
};
