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
        Schema::table('aduan_ict', function (Blueprint $table) {
            $table->foreignId('pentadbir_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
            $table->text('catatan_pentadbir')->nullable()->after('status');
            $table->timestamp('tarikh_selesai')->nullable()->after('catatan_pentadbir');
        });
    }

    public function down(): void
    {
        Schema::table('aduan_ict', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pentadbir_id');
            $table->dropColumn(['catatan_pentadbir', 'tarikh_selesai']);
        });
    }
};
