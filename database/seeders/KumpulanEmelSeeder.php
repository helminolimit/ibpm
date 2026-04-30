<?php

namespace Database\Seeders;

use App\Models\KumpulanEmel;
use Illuminate\Database\Seeder;

class KumpulanEmelSeeder extends Seeder
{
    public function run(): void
    {
        $kumpulan = [
            ['nama_kumpulan' => 'Semua Pegawai MOTAC', 'alamat_emel' => 'semua@motac.gov.my', 'pemilik_unit' => 'Unit Infrastruktur & Keselamatan ICT', 'jumlah_ahli' => 0],
            ['nama_kumpulan' => 'Bahagian Pengurusan Maklumat', 'alamat_emel' => 'bpm@motac.gov.my', 'pemilik_unit' => 'Unit Infrastruktur & Keselamatan ICT', 'jumlah_ahli' => 0],
            ['nama_kumpulan' => 'Unit Infrastruktur & Keselamatan ICT', 'alamat_emel' => 'infrastruktur@motac.gov.my', 'pemilik_unit' => 'Unit Infrastruktur & Keselamatan ICT', 'jumlah_ahli' => 0],
            ['nama_kumpulan' => 'Unit Aplikasi & Pembangunan Sistem', 'alamat_emel' => 'aplikasi@motac.gov.my', 'pemilik_unit' => 'Unit Aplikasi & Pembangunan Sistem', 'jumlah_ahli' => 0],
            ['nama_kumpulan' => 'Unit Sokongan Pengguna', 'alamat_emel' => 'sokongan@motac.gov.my', 'pemilik_unit' => 'Unit Sokongan Pengguna', 'jumlah_ahli' => 0],
            ['nama_kumpulan' => 'Bahagian Kewangan', 'alamat_emel' => 'kewangan@motac.gov.my', 'pemilik_unit' => null, 'jumlah_ahli' => 0],
            ['nama_kumpulan' => 'Bahagian Sumber Manusia', 'alamat_emel' => 'hrm@motac.gov.my', 'pemilik_unit' => null, 'jumlah_ahli' => 0],
        ];

        foreach ($kumpulan as $data) {
            KumpulanEmel::firstOrCreate(['alamat_emel' => $data['alamat_emel']], $data);
        }
    }
}
