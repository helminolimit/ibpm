<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, we need to recreate the column since ALTER COLUMN is limited.
        // For MySQL, we can modify the enum directly.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN, so we recreate the table constraint
            // by dropping and recreating the column
            Schema::table('notifikasi_portals', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });

            Schema::table('notifikasi_portals', function (Blueprint $table) {
                $table->enum('jenis', ['permohonan_baru', 'status_dikemaskini', 'tugasan_baru'])->after('permohonan_portal_id');
            });
        } else {
            DB::statement("ALTER TABLE notifikasi_portals MODIFY COLUMN jenis ENUM('permohonan_baru', 'status_dikemaskini', 'tugasan_baru') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('notifikasi_portals', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });

            Schema::table('notifikasi_portals', function (Blueprint $table) {
                $table->enum('jenis', ['permohonan_baru', 'status_dikemaskini'])->after('permohonan_portal_id');
            });
        } else {
            DB::statement("ALTER TABLE notifikasi_portals MODIFY COLUMN jenis ENUM('permohonan_baru', 'status_dikemaskini') NOT NULL");
        }
    }
};
