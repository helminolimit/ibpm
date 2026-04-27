<?php

namespace Database\Seeders;

use App\Models\KategoriAduan;
use Illuminate\Database\Seeder;

class KategoriAduanSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            [
                'nama' => 'Kerosakan Perkakasan',
                'unit_bpm' => 'Unit Infrastruktur',
                'emel_unit' => 'infrastruktur@motac.gov.my',
            ],
            [
                'nama' => 'Masalah Perisian / Aplikasi',
                'unit_bpm' => 'Unit Aplikasi',
                'emel_unit' => 'aplikasi@motac.gov.my',
            ],
            [
                'nama' => 'Masalah Rangkaian / Internet',
                'unit_bpm' => 'Unit Infrastruktur',
                'emel_unit' => 'infrastruktur@motac.gov.my',
            ],
            [
                'nama' => 'Akaun & Akses Pengguna',
                'unit_bpm' => 'Unit Sokongan Pengguna',
                'emel_unit' => 'sokongan@motac.gov.my',
            ],
            [
                'nama' => 'E-mel & Komunikasi',
                'unit_bpm' => 'Unit Sokongan Pengguna',
                'emel_unit' => 'sokongan@motac.gov.my',
            ],
            [
                'nama' => 'Keselamatan ICT',
                'unit_bpm' => 'Unit Keselamatan ICT',
                'emel_unit' => 'keselamatan@motac.gov.my',
            ],
            [
                'nama' => 'Lain-lain',
                'unit_bpm' => 'Unit Sokongan Pengguna',
                'emel_unit' => 'sokongan@motac.gov.my',
            ],
        ];

        foreach ($kategori as $item) {
            KategoriAduan::firstOrCreate(
                ['nama' => $item['nama']],
                array_merge($item, ['is_aktif' => true]),
            );
        }
    }
}
