<?php

namespace App\Http\Controllers;

use App\Models\KelulusanPenamatan;
use App\Models\PermohonanPenamatan;
use App\Notifications\PenatamatanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelulusanPeringkat1Controller extends Controller
{
    public function index()
    {
        $senarai = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_1')
            ->with(['pemohon', 'penggunaSasaran'])
            ->latest()
            ->paginate(15);

        return view('m03.kelulusan1.index', compact('senarai'));
    }

    public function lulus($id)
    {
        $permohonan = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_1')->findOrFail($id);

        KelulusanPenamatan::create([
            'permohonan_penamatan_id' => $permohonan->id,
            'pelulus_id' => Auth::id(),
            'peringkat' => 'PERINGKAT_1',
            'keputusan' => 'LULUS',
            'diluluskan_pada' => now(),
        ]);

        $permohonan->update(['status' => 'MENUNGGU_KEL_2']);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'kelulusan_peringkat_1',
            'modul' => 'M03',
            'ip_address' => request()->ip(),
        ]);

        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'KELULUSAN'));

        return redirect()->route('kelulusan.penamatan.index')
            ->with('berjaya', 'Permohonan '.$permohonan->no_tiket.' diluluskan.');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:5|max:500']);

        $permohonan = PermohonanPenamatan::where('status', 'MENUNGGU_KEL_1')->findOrFail($id);

        KelulusanPenamatan::create([
            'permohonan_penamatan_id' => $permohonan->id,
            'pelulus_id' => Auth::id(),
            'peringkat' => 'PERINGKAT_1',
            'keputusan' => 'TOLAK',
            'catatan' => $request->catatan,
            'diluluskan_pada' => now(),
        ]);

        $permohonan->update(['status' => 'DITOLAK']);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'tolak_peringkat_1',
            'modul' => 'M03',
            'ip_address' => request()->ip(),
        ]);

        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'TOLAK'));

        return redirect()->route('kelulusan.penamatan.index')
            ->with('maklum', 'Permohonan '.$permohonan->no_tiket.' ditolak.');
    }
}
