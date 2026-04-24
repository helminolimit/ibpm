<?php

namespace Database\Seeders;

use App\Enums\StatusAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\User;
use Illuminate\Database\Seeder;

class AduanIctSeeder extends Seeder
{
    public function run(): void
    {
        if (AduanIct::count() > 0) {
            return;
        }

        $pengguna = User::where('email', 'test@example.com')->firstOrFail();
        $pentadbirs = User::where('role', 'pentadbir')->get();
        $kategori = KategoriAduan::all();

        $scenarios = [
            ['status' => StatusAduan::Baru],
            ['status' => StatusAduan::Baru],
            ['status' => StatusAduan::Baru],
            [
                'status' => StatusAduan::DalamProses,
                'pentadbir_id' => $pentadbirs->random()->id,
            ],
            [
                'status' => StatusAduan::DalamProses,
                'pentadbir_id' => $pentadbirs->random()->id,
            ],
            [
                'status' => StatusAduan::Selesai,
                'pentadbir_id' => $pentadbirs->random()->id,
                'catatan_pentadbir' => 'Aduan telah diselesaikan. Perkakasan telah diganti dan sistem berjalan dengan baik.',
                'tarikh_selesai' => now()->subDays(3),
            ],
            [
                'status' => StatusAduan::Selesai,
                'pentadbir_id' => $pentadbirs->random()->id,
                'catatan_pentadbir' => 'Masalah perisian telah diperbaiki. Sila hubungi kami jika masalah berulang.',
                'tarikh_selesai' => now()->subDays(7),
            ],
            [
                'status' => StatusAduan::Ditolak,
                'pentadbir_id' => $pentadbirs->random()->id,
                'catatan_pentadbir' => 'Aduan tidak berkaitan dengan skop BPM. Sila hubungi bahagian berkenaan.',
            ],
            ['status' => StatusAduan::Dibatalkan],
        ];

        foreach ($scenarios as $overrides) {
            AduanIct::factory()->create(array_merge([
                'no_tiket' => AduanIct::generateNoTiket(),
                'user_id' => $pengguna->id,
                'kategori_aduan_id' => $kategori->random()->id,
            ], $overrides));
        }
    }
}
