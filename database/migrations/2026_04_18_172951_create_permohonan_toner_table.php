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
        Schema::create('permohonan_toner', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('model_pencetak', 100);
            $table->string('jenama_toner', 100);
            $table->string('jenis_toner');
            $table->string('no_siri_toner', 100)->nullable();
            $table->unsignedSmallInteger('kuantiti');
            $table->string('lokasi_pencetak', 150);
            $table->text('tujuan');
            $table->date('tarikh_diperlukan')->nullable();
            $table->string('status')->default('submitted');
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
        Schema::dropIfExists('permohonan_toner');
    }
};
