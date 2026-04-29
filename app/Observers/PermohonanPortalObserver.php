<?php

namespace App\Observers;

use App\Mail\StatusPortalDikemaskini;
use App\Models\NotifikasiPortal;
use App\Models\PermohonanPortal;
use Illuminate\Support\Facades\Mail;

class PermohonanPortalObserver
{
    /**
     * Handle the PermohonanPortal "updated" event.
     */
    public function updated(PermohonanPortal $permohonan): void
    {
        // Check if status was changed
        if ($permohonan->isDirty('status')) {
            $statusLama = $permohonan->getOriginal('status');
            $statusBaru = $permohonan->status->value;

            // Hantar email ke pemohon (queue)
            Mail::to($permohonan->pemohon->email)->queue(new StatusPortalDikemaskini($permohonan));

            // Simpan notifikasi
            NotifikasiPortal::create([
                'pengguna_id' => $permohonan->pemohon_id,
                'permohonan_portal_id' => $permohonan->id,
                'jenis' => 'status_dikemaskini',
                'mesej' => "Status permohonan {$permohonan->no_tiket} telah dikemaskini kepada {$permohonan->status->label()}.",
            ]);

            // Log audit
            $permohonan->logAudits()->create([
                'pengguna_id' => auth()->id() ?? $permohonan->pentadbir_id,
                'tindakan' => 'status_dikemaskini',
                'modul' => 'M04',
                'ip_address' => request()->ip(),
                'butiran' => [
                    'status_lama' => $statusLama,
                    'status_baru' => $statusBaru,
                ],
            ]);
        }
    }
}
