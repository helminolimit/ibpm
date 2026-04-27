<?php

namespace App\Listeners;

use App\Enums\StatusNotifikasi;
use App\Events\AduanBaru;
use App\Mail\NotifikasiAduanBaru;
use App\Models\Notifikasi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HantarEmailNotifikasiAdmin implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public array $backoff = [1, 5, 10];

    public function handle(AduanBaru $event): void
    {
        $aduan = $event->aduan->load(['user', 'kategori']);
        $emelUnit = $aduan->kategori->emel_unit;

        if (! $emelUnit) {
            return;
        }

        try {
            Mail::to($emelUnit)->send(new NotifikasiAduanBaru($aduan));

            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'makluman',
                'penerima' => $emelUnit,
                'status' => StatusNotifikasi::Hantar,
            ]);
        } catch (\Throwable $e) {
            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'makluman',
                'penerima' => $emelUnit,
                'status' => StatusNotifikasi::Gagal,
                'ralat' => $e->getMessage(),
            ]);

            Log::error('Gagal hantar emel notifikasi admin aduan', [
                'aduan_id' => $aduan->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
