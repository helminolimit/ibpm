<?php

namespace Database\Factories;

use App\Enums\StatusPermohonanPenamatan;
use App\Models\PermohonanPenamatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PermohonanPenamatan>
 */
class PermohonanPenamatanFactory extends Factory
{
    public function definition(): array
    {
        static $counter = 0;
        $counter++;
        $year = now()->year;

        return [
            'no_tiket' => sprintf('PAK-%d-%03d', $year, $counter),
            'pemohon_id' => User::factory(),
            'pengguna_sasaran_id' => User::factory(),
            'id_login_komputer' => fake()->userName(),
            'tarikh_berkuat_kuasa' => now()->addDays(7)->toDateString(),
            'jenis_tindakan' => fake()->randomElement(['TAMAT', 'GANTUNG']),
            'sebab_penamatan' => fake()->sentence(15),
            'status' => StatusPermohonanPenamatan::MenungguKel1,
        ];
    }

    public function draf(): static
    {
        return $this->state(['status' => StatusPermohonanPenamatan::Draf]);
    }

    public function menungguKel1(): static
    {
        return $this->state(['status' => StatusPermohonanPenamatan::MenungguKel1]);
    }

    public function menungguKel2(): static
    {
        return $this->state(['status' => StatusPermohonanPenamatan::MenungguKel2]);
    }

    public function dalamProses(): static
    {
        return $this->state(['status' => StatusPermohonanPenamatan::DalamProses]);
    }

    public function selesai(): static
    {
        return $this->state([
            'status' => StatusPermohonanPenamatan::Selesai,
            'tarikh_selesai' => now(),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(['status' => StatusPermohonanPenamatan::Ditolak]);
    }
}
