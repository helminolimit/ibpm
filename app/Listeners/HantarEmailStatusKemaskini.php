<?php

namespace App\Listeners;

use App\Enums\StatusAduan;
use App\Enums\StatusNotifikasi;
use App\Events\StatusDikemaskini;
use App\Mail\StatusKemaskinanMail;
use App\Models\Notifikasi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HantarEmailStatusKemaskini implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public array $backoff = [1, 5, 10];

    public function handle(StatusDikemaskini $event): void
    {
        if ($event->status !== StatusAduan::DalamProses) {
            return;
        }

        $aduan = $event->aduan->load(['user', 'kategori']);
        $penerima = $aduan->user->email;

        try {
            Mail::to($penerima)->send(new StatusKemaskinanMail($aduan));

            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'kemaskini_status',
                'penerima' => $penerima,
                'status' => StatusNotifikasi::Hantar,
            ]);
        } catch (\Throwable $e) {
            Notifikasi::create([
                'aduan_ict_id' => $aduan->id,
                'jenis' => 'kemaskini_status',
                'penerima' => $penerima,
                'status' => StatusNotifikasi::Gagal,
                'ralat' => $e->getMessage(),
            ]);

            Log::error('Gagal hantar emel kemaskini status aduan', [
                'aduan_id' => $aduan->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
