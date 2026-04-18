<?php

namespace App\Listeners;

use App\Enums\StatusNotifikasi;
use App\Events\AduanDitugaskan;
use App\Mail\AduanDitugaskanMail;
use App\Models\Notifikasi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HantarEmailAduanDitugaskan implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public array $backoff = [1, 5, 10];

    public function handle(AduanDitugaskan $event): void
    {
        $aduan = $event->aduan->load(['user', 'kategori']);
        $teknician = $event->teknician;
        $penerima = $teknician->email;

        try {
            Mail::to($penerima)->send(new AduanDitugaskanMail($aduan, $teknician));

            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'ditugaskan',
                'penerima' => $penerima,
                'status' => StatusNotifikasi::Hantar,
            ]);
        } catch (\Throwable $e) {
            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'ditugaskan',
                'penerima' => $penerima,
                'status' => StatusNotifikasi::Gagal,
                'ralat' => $e->getMessage(),
            ]);

            Log::error('Gagal hantar emel aduan ditugaskan', [
                'aduan_id' => $aduan->id,
                'teknician_id' => $teknician->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
