<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_toner', function (Blueprint $table) {
            $table->string('model_toner', 100)->nullable()->after('id');
            $table->string('jenama', 100)->nullable()->after('model_toner');
            $table->string('warna', 100)->nullable()->after('jenama');
            $table->unsignedInteger('kuantiti_minimum')->default(1)->after('kuantiti_ada');
            $table->dropUnique(['jenis_toner']);
        });
    }

    public function down(): void
    {
        Schema::table('stok_toner', function (Blueprint $table) {
            $table->dropColumn(['model_toner', 'jenama', 'warna', 'kuantiti_minimum']);
            $table->unique('jenis_toner');
        });
    }
};
