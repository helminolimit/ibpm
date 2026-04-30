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
        Schema::create('tugasan_portals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_portal_id')->constrained('permohonan_portals')->cascadeOnDelete();
            $table->foreignId('teknisian_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ditugaskan_oleh')->constrained('users')->cascadeOnDelete();
            $table->text('nota_tugasan')->nullable();
            $table->string('status_tugasan')->default('baharu');
            $table->timestamp('tarikh_tugasan')->useCurrent();
            $table->timestamps();

            $table->index(['permohonan_portal_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugasan_portals');
    }
};
