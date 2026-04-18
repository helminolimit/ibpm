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
        ]);

        $this->call(KategoriAduanSeeder::class);
    }
}
