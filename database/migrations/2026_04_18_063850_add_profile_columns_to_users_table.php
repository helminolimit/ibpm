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
        Schema::table('users', function (Blueprint $table) {
            $table->string('bahagian')->nullable()->after('email');
            $table->string('jawatan')->nullable()->after('bahagian');
            $table->string('no_telefon')->nullable()->after('jawatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bahagian', 'jawatan', 'no_telefon']);
        });
    }
};
