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
        Schema::table('permohonan_toner', function (Blueprint $table) {
            $table->unsignedSmallInteger('kuantiti_diluluskan')->nullable()->after('kuantiti');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_toner', function (Blueprint $table) {
            $table->dropColumn('kuantiti_diluluskan');
        });
    }
};
