<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'bahagian' => 'Bahagian Pengurusan Maklumat',
            'jawatan' => 'Pegawai Teknologi Maklumat',
            'no_telefon' => '03-88891234',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Superadmin',
            'email' => 'admin@motac.gov.my',
            'bahagian' => 'Bahagian Pengurusan Maklumat',
            'jawatan' => 'Pegawai Teknologi Maklumat',
            'no_telefon' => '03-88891234',
            'role' => 'superadmin',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Unit Infrastruktur',
            'email' => 'infrastruktur@motac.gov.my',
            'bahagian' => 'Bahagian Pengurusan Maklumat',
            'unit_bpm' => 'Unit Infrastruktur',
            'jawatan' => 'Pegawai Teknologi Maklumat',
            'no_telefon' => '03-88891234',
            'role' => 'pentadbir',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Unit Aplikasi',
            'email' => 'aplikasi@motac.gov.my',
            'bahagian' => 'Bahagian Pengurusan Maklumat',
            'unit_bpm' => 'Unit Aplikasi',
            'jawatan' => 'Pegawai Teknologi Maklumat',
            'no_telefon' => '03-88891234',
            'role' => 'pentadbir',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Unit Sokongan Pengguna',
            'email' => 'sokongan@motac.gov.my',
            'bahagian' => 'Bahagian Pengurusan Maklumat',
            'unit_bpm' => 'Unit Sokongan Pengguna',
            'jawatan' => 'Pegawai Teknologi Maklumat',
            'no_telefon' => '03-88891234',
            'role' => 'pentadbir',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Unit Keselamatan',
            'email' => 'keselamatan@motac.gov.my',
            'bahagian' => 'Bahagian Pengurusan Maklumat',
            'unit_bpm' => 'Unit Keselamatan',
            'jawatan' => 'Pegawai Teknologi Maklumat',
            'no_telefon' => '03-88891234',
            'role' => 'pentadbir',
            'password' => '12345678',
        ]);

        $this->call([
            PenggunaTeknisianSeeder::class,
            KategoriAduanSeeder::class,
            AduanIctSeeder::class,
            KumpulanEmelSeeder::class,
        ]);
    }
}
