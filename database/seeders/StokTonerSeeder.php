<?php

namespace Database\Seeders;

use App\Models\StokToner;
use Illuminate\Database\Seeder;

class StokTonerSeeder extends Seeder
{
    public function run(): void
    {
        $jenisToner = ['hitam', 'cyan', 'magenta', 'kuning'];

        foreach ($jenisToner as $jenis) {
            StokToner::firstOrCreate(
                ['jenis_toner' => $jenis],
                ['kuantiti_ada' => 0],
            );
        }
    }
}
