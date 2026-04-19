<?php

namespace Database\Seeders;

use App\Models\StokToner;
use Illuminate\Database\Seeder;

class StokTonerSeeder extends Seeder
{
    public function run(): void
    {
        $stok = [
            ['jenis_toner' => 'hitam', 'model_toner' => 'Umum', 'jenama' => 'Pelbagai', 'kuantiti_minimum' => 5],
            ['jenis_toner' => 'cyan', 'model_toner' => 'Umum', 'jenama' => 'Pelbagai', 'kuantiti_minimum' => 5],
            ['jenis_toner' => 'magenta', 'model_toner' => 'Umum', 'jenama' => 'Pelbagai', 'kuantiti_minimum' => 5],
            ['jenis_toner' => 'kuning', 'model_toner' => 'Umum', 'jenama' => 'Pelbagai', 'kuantiti_minimum' => 5],
        ];

        foreach ($stok as $item) {
            StokToner::firstOrCreate(
                ['jenis_toner' => $item['jenis_toner'], 'model_toner' => null, 'jenama' => null],
                array_merge($item, ['kuantiti_ada' => 0]),
            );
        }
    }
}
