<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bahagian')) {
                $table->string('bahagian')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'jawatan')) {
                $table->string('jawatan')->nullable()->after('bahagian');
            }

            if (! Schema::hasColumn('users', 'no_telefon')) {
                $table->string('no_telefon')->nullable()->after('jawatan');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('no_telefon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(
                collect(['bahagian', 'jawatan', 'no_telefon', 'role'])
                    ->filter(fn ($col) => Schema::hasColumn('users', $col))
                    ->values()
                    ->all()
            );
        });
    }
};
