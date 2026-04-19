<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_toner', function (Blueprint $table) {
            $table->dropForeign(['permohonan_toner_id']);
            $table->dropIndex(['permohonan_toner_id']);
            $table->foreignId('permohonan_toner_id')->nullable()->change();
            $table->foreign('permohonan_toner_id')->references('id')->on('permohonan_toner')->nullOnDelete();
            $table->index('permohonan_toner_id');
        });
    }

    public function down(): void
    {
        Schema::table('log_toner', function (Blueprint $table) {
            $table->dropForeign(['permohonan_toner_id']);
            $table->dropIndex(['permohonan_toner_id']);
            $table->foreignId('permohonan_toner_id')->nullable(false)->change();
            $table->foreign('permohonan_toner_id')->references('id')->on('permohonan_toner')->cascadeOnDelete();
            $table->index('permohonan_toner_id');
        });
    }
};
