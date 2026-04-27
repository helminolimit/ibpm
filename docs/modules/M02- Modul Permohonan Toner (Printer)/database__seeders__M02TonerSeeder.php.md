# `database/seeders/M02TonerSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\StokToner;
use App\Models\PermohonanToner;
use App\Models\User;
use Illuminate\Database\Seeder;

class M02TonerSeeder extends Seeder
{
    public function run(): void
    {
        // Stok toner awal
        $stokData = [
            ['model_toner' => 'CF217A',  'jenama' => 'HP',    'jenis' => 'hitam',   'kuantiti_ada' => 20, 'kuantiti_minimum' => 5],
            ['model_toner' => 'CF410A',  'jenama' => 'HP',    'jenis' => 'hitam',   'kuantiti_ada' => 10, 'kuantiti_minimum' => 3],
            ['model_toner' => 'CF411A',  'jenama' => 'HP',    'jenis' => 'cyan',    'kuantiti_ada' => 8,  'kuantiti_minimum' => 3],
            ['model_toner' => 'CF412A',  'jenama' => 'HP',    'jenis' => 'kuning',  'kuantiti_ada' => 6,  'kuantiti_minimum' => 3],
            ['model_toner' => 'CF413A',  'jenama' => 'HP',    'jenis' => 'magenta', 'kuantiti_ada' => 4,  'kuantiti_minimum' => 3],
            ['model_toner' => '045H BK', 'jenama' => 'Canon', 'jenis' => 'hitam',   'kuantiti_ada' => 12, 'kuantiti_minimum' => 4],
        ];

        foreach ($stokData as $stok) {
            StokToner::firstOrCreate(
                ['model_toner' => $stok['model_toner'], 'jenama' => $stok['jenama'], 'jenis' => $stok['jenis']],
                $stok
            );
        }

        // Contoh permohonan
        $pemohon = User::where('peranan', 'pemohon')->first();
        if ($pemohon) {
            PermohonanToner::firstOrCreate(
                ['no_tiket' => '#TON-2026-001'],
                [
                    'pemohon_id'       => $pemohon->id,
                    'model_pencetak'   => 'HP LaserJet Pro M404n',
                    'jenama_toner'     => 'HP',
                    'jenis_toner'      => 'hitam',
                    'kuantiti_diminta' => 2,
                    'lokasi_pencetak'  => 'Bilik 3.01, Tingkat 3',
                    'bahagian_pemohon' => 'Bahagian Pengurusan Maklumat',
                    'tujuan'           => 'Toner pencetak hampir habis, diperlukan untuk operasi harian.',
                    'status'           => 'submitted',
                    'submitted_at'     => now(),
                ]
            );
        }
    }
}
```
