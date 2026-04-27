<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PenggunaTeknisianSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = [
            [
                'name' => 'Ahmad Fadzillah',
                'email' => 'ahmad.fadzillah@motac.gov.my',
                'bahagian' => 'Bahagian Perancangan Strategik',
                'jawatan' => 'Pegawai Tadbir',
                'no_telefon' => '03-88891001',
            ],
            [
                'name' => 'Siti Aisyah',
                'email' => 'siti.aisyah@motac.gov.my',
                'bahagian' => 'Bahagian Kewangan',
                'jawatan' => 'Penolong Akauntan',
                'no_telefon' => '03-88891002',
            ],
            [
                'name' => 'Mohd Hafizuddin',
                'email' => 'mohd.hafizuddin@motac.gov.my',
                'bahagian' => 'Bahagian Sumber Manusia',
                'jawatan' => 'Pembantu Tadbir',
                'no_telefon' => '03-88891003',
            ],
            [
                'name' => 'Nurul Hidayah',
                'email' => 'nurul.hidayah@motac.gov.my',
                'bahagian' => 'Bahagian Industri Pelancongan',
                'jawatan' => 'Pegawai Pelancongan',
                'no_telefon' => '03-88891004',
            ],
            [
                'name' => 'Razif Ismail',
                'email' => 'razif.ismail@motac.gov.my',
                'bahagian' => 'Bahagian Pembangunan Produk',
                'jawatan' => 'Penolong Pengarah',
                'no_telefon' => '03-88891005',
            ],
        ];

        foreach ($pengguna as $data) {
            User::factory()->create(array_merge($data, ['password' => '12345678']));
        }

        $teknisian = [
            [
                'name' => 'Khairul Anam',
                'email' => 'khairul.anam@motac.gov.my',
                'bahagian' => 'Bahagian Pengurusan Maklumat',
                'unit_bpm' => 'Unit Infrastruktur',
                'jawatan' => 'Pegawai Teknologi Maklumat',
                'no_telefon' => '03-88891101',
            ],
            [
                'name' => 'Faridah Noor',
                'email' => 'faridah.noor@motac.gov.my',
                'bahagian' => 'Bahagian Pengurusan Maklumat',
                'unit_bpm' => 'Unit Aplikasi',
                'jawatan' => 'Pembantu Teknologi Maklumat',
                'no_telefon' => '03-88891102',
            ],
            [
                'name' => 'Zulkifli Rahman',
                'email' => 'zulkifli.rahman@motac.gov.my',
                'bahagian' => 'Bahagian Pengurusan Maklumat',
                'unit_bpm' => 'Unit Sokongan Pengguna',
                'jawatan' => 'Pegawai Teknologi Maklumat',
                'no_telefon' => '03-88891103',
            ],
            [
                'name' => 'Hasniza Daud',
                'email' => 'hasniza.daud@motac.gov.my',
                'bahagian' => 'Bahagian Pengurusan Maklumat',
                'unit_bpm' => 'Unit Keselamatan',
                'jawatan' => 'Pembantu Teknologi Maklumat',
                'no_telefon' => '03-88891104',
            ],
        ];

        foreach ($teknisian as $data) {
            User::factory()->teknician($data['unit_bpm'])->create(array_merge($data, ['password' => '12345678']));
        }
    }
}
