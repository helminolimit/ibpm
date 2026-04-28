<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelulusanPenamatan;
use App\Models\PermohonanPenamatan;
use App\Notifications\PenatamatanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenatamatanAdminController extends Controller
{
    public function index(Request $request)
    {
        $senarai = PermohonanPenamatan::with(['pemohon', 'penggunaSasaran'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('m03.admin.index', compact('senarai'));
    }

    public function lulus($id)
    {
        $permohonan = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_2')->findOrFail($id);

        KelulusanPenamatan::create([
            'permohonan_penamatan_id' => $permohonan->id,
            'pelulus_id' => Auth::id(),
            'peringkat' => 'PERINGKAT_2',
            'keputusan' => 'LULUS',
            'diluluskan_pada' => now(),
        ]);

        $permohonan->update(['status' => 'DALAM_PROSES']);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'kelulusan_peringkat_2',
            'modul' => 'M03',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.penamatan.index')
            ->with('berjaya', 'Permohonan '.$permohonan->no_tiket.' diluluskan. Sila laksanakan penamatan.');
    }

    public function selesai(Request $request, $id)
    {
        $permohonan = PermohonanPenamatan::where('status', 'DALAM_PROSES')->findOrFail($id);

        $permohonan->update([
            'status' => 'SELESAI',
            'tarikh_selesai' => now(),
        ]);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'akaun_ditamatkan',
            'modul' => 'M03',
            'ip_address' => request()->ip(),
        ]);

        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'SELESAI'));

        $permohonan->notifikasi()->create([
            'penerima_id' => $permohonan->pemohon_id,
            'jenis' => 'SELESAI',
            'tajuk' => 'Akaun '.$permohonan->id_login_komputer.' berjaya ditamatkan',
            'mesej' => 'Permohonan '.$permohonan->no_tiket.' telah selesai diproses.',
            'dihantar_pada' => now(),
        ]);

        return redirect()->route('admin.penamatan.index')
            ->with('berjaya', 'Akaun '.$permohonan->id_login_komputer.' berjaya ditamatkan.');
    }

    public function audit($id)
    {
        $permohonan = PermohonanPenamatan::with([
            'logAudit' => fn ($q) => $q->with('pengguna')->orderByDesc('created_at'),
            'pemohon',
            'penggunaSasaran',
        ])->findOrFail($id);

        return view('m03.admin.audit', compact('permohonan'));
    }
}
