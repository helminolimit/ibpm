<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_penamatan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('pemohon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pengguna_sasaran_id')->constrained('users')->cascadeOnDelete();
            $table->string('id_login_komputer');
            $table->date('tarikh_berkuat_kuasa');
            $table->enum('jenis_tindakan', ['TAMAT', 'GANTUNG']);
            $table->text('sebab_penamatan');
            $table->string('status')->default('DRAF');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('tarikh_selesai')->nullable();
            $table->timestamps();

            $table->index(['pemohon_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_penamatan');
    }
};
