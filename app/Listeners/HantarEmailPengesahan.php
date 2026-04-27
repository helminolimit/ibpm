<?php

namespace App\Listeners;

use App\Enums\StatusNotifikasi;
use App\Events\AduanDihantar;
use App\Mail\PengesahanAduan;
use App\Models\Notifikasi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HantarEmailPengesahan implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public array $backoff = [1, 5, 10];

    public function handle(AduanDihantar $event): void
    {
        $aduan = $event->aduan->load(['user', 'kategori']);
        $penerima = $aduan->user->email;

        try {
            Mail::to($penerima)->send(new PengesahanAduan($aduan));

            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'pengesahan',
                'penerima' => $penerima,
                'status' => StatusNotifikasi::Hantar,
            ]);
        } catch (\Throwable $e) {
            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'pengesahan',
                'penerima' => $penerima,
                'status' => StatusNotifikasi::Gagal,
                'ralat' => $e->getMessage(),
            ]);

            Log::error('Gagal hantar emel pengesahan aduan', [
                'aduan_id' => $aduan->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
